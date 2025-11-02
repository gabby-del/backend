<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Models\Budget; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RequestApproved; 
use App\Notifications\RequestPaid;
use App\Notifications\RequestSubmitted;
use App\Models\AuditLog;
use App\Models\User; 

class PaymentRequestController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of requests, filtered by user's access role.
     * Corresponds to: GET /api/v1/requests
     */
    public function index()
    {
        $user = Auth::user();

        // Policy scope (viewAny) should be used here to filter requests based on user role.
        // E.g., CEO/FM/FO see all; others see only their own and perhaps their department's.
        $query = $user->can('viewAny', PaymentRequest::class)
             ? PaymentRequest::query() 
             : $user->paymentRequests()->orWhere('department_id', $user->department_id); 

        // Ensure all required relationships are eager loaded
        $requests = $query->with(['user', 'department', 'project'])->latest()->get();

        return response()->json($requests);
    }

    // Creates a new request (POST /api/v1/requests)
    public function store(Request $request)
    {
        // ... (store method remains unchanged) ...
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'department_id' => 'required|exists:departments,id',
            // ... (Add validation for project_id, vendor details, etc.)
            'project_id' => 'nullable|exists:projects,id',
            'vendor_name' => 'required|string',
            'vendor_details' => 'required|string',
            'expense_category' => 'required|string',
        ]);

        $requestData = array_merge($validated, [
            'user_id' => Auth::id(), 
            'status' => 'draft',
        ]);

        $paymentRequest = PaymentRequest::create($requestData);
        
        // Audit Log: Record creation
        AuditLog::create([
            'user_id' => auth()->id,
            'auditable_type' => $paymentRequest::class,
            'auditable_id' => $paymentRequest->id,
            'action' => 'created_draft',
            'new_values' => json_encode(['status' => 'draft']),
        ]);

        return response()->json($paymentRequest, 201);
    }

    /**
     * Method to change status to 'pending' from 'draft' (Submission)
     */
    public function submit(PaymentRequest $paymentRequest) 
    {
        $this->authorize('submit', $paymentRequest); 

        $oldStatus = $paymentRequest->status;

        $paymentRequest->update(['status' => 'pending', 'submitted_at' => now()]);

        
        $financeManager = User::whereHas('role', fn($q) => $q->where('name', 'Finance Manager'))->first();
        
        if ($financeManager) {
            
             $financeManager->notify(new RequestSubmitted($paymentRequest)); 
        }

        // Audit Log: Record submission
        AuditLog::create([
            'user_id' => auth()->id,
            'auditable_type' => $paymentRequest::class,
            'auditable_id' => $paymentRequest->id,
            'action' => 'submitted',
            'old_values' => json_encode(['status' => $oldStatus]),
            'new_values' => json_encode(['status' => 'pending']),
        ]);

        return response()->json($paymentRequest);
    }
    
    /**
     * Approval Workflow Action
     */
    public function approve(PaymentRequest $paymentRequest) 
    {
        $this->authorize('approve', $paymentRequest);

        //  BUDGET AUTO-VALIDATION (Requirement 3: Budget Limits Enforcement)

        // 1. Find the active budget for the relevant department
        $budgetQuery = Budget::where('department_id', $paymentRequest->department_id)
            ->where('status', 'Active');

        // Optional: Filter by project if project_id is set
        if ($paymentRequest->project_id) {
             $budgetQuery->where('project_id', $paymentRequest->project_id);
        }
        
        $budget = $budgetQuery->first();

        // 2. Check if a valid, active budget exists
        if (!$budget) {
            return response()->json(['message' => 'Cannot approve. No active budget found for this expenditure.'], 400);
        }

        // 3. Check the limit
        $availableAmount = $budget->amount_allocated - $budget->amount_spent;

        if ($paymentRequest->amount > $availableAmount) {
            // Auto-reject and log the reason
            AuditLog::create([
                'user_id' => auth()->id,
                'auditable_type' => $paymentRequest::class,
                'auditable_id' => $paymentRequest->id,
                'action' => 'rejected_auto_budget',
                'new_values' => json_encode(['reason' => 'Exceeds Budget Limit']),
            ]);
            
            return response()->json([
                'message' => 'Request exceeds the available budget limit and cannot be approved.',
                'available_budget' => $availableAmount,
            ], 403); // Use 403 Forbidden for insufficient resources
        }
        
        // ----------------- END AUTO-VALIDATION -----------------

        $oldStatus = $paymentRequest->status;
        
        // 4. Perform the critical status update (if validation passes)
        $paymentRequest->update(['status' => 'approved', 'approved_at' => now()]);

        // 5. Finalize Audit Trail Logging
        AuditLog::create([
            'user_id' => auth()->id,
            'auditable_type' => $paymentRequest::class,
            'auditable_id' => $paymentRequest->id,
            'action' => 'approved',
            'old_values' => json_encode(['status' => $oldStatus]),
            'new_values' => json_encode(['status' => 'approved']),
        ]);

        // 6. Dispatch notification to Requester
        $paymentRequest->user->notify(new RequestApproved($paymentRequest));

        return response()->json($paymentRequest);
    }

    /**
     * Payment Action (Final debit and status update)
     */
    public function markAsPaid(PaymentRequest $paymentRequest)
    {
        $this->authorize('pay', $paymentRequest);

        // Ensure the request is approved before paying
        if ($paymentRequest->status !== 'approved') {
            return response()->json(['message' => 'Payment request must be approved before payment can be finalized.'], 400);
        }

        //  REAL-TIME TRACKING (Requirement 4: Debit the Budget)

        // 1. Find the active budget
        $budgetQuery = Budget::where('department_id', $paymentRequest->department_id)
            ->where('status', 'Active');
            
        if ($paymentRequest->project_id) {
            $budgetQuery->where('project_id', $paymentRequest->project_id);
        }

        // Use firstOrFail; if no budget is found, this should throw an exception.
        $budget = $budgetQuery->firstOrFail(); 

        // 2. Debit the budget's spent amount
        $budget->amount_spent += $paymentRequest->amount;
        $budget->save();

        // ----------------- END REAL-TIME TRACKING -----------------

        $oldStatus = $paymentRequest->status;

        // 3. Update request status
        $paymentRequest->update(['status' => 'paid', 'paid_at' => now()]);

        $paymentRequest->update(['status' => 'paid', 'paid_at' => now()]);

// ADDED: Notification to Requester upon final payment
     $paymentRequest->user->notify(new RequestPaid($paymentRequest)); 

        // Audit Log: Record payment
        AuditLog::create([
            'user_id' => auth()->id,
            'auditable_type' => $paymentRequest::class,
            'auditable_id' => $paymentRequest->id,
            'action' => 'marked_as_paid',
            'old_values' => json_encode(['status' => $oldStatus]),
            'new_values' => json_encode(['status' => 'paid']),
        ]);

        return response()->json($paymentRequest);
    }
}