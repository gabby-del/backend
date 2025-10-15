<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment_request extends Model
{
    use HasFactory;
    protected $table = 'payment_requests';
    protected $fillable = [
        'requester_id',
        'amount',
        'currency',
        'cost_center_id',
        'budget_id',
        'description',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];
}
