<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
class NotificationController extends Controller
{
    /**
     * Display a listing of all unread and read notifications for the authenticated user.
     * Corresponds to: GET /api/v1/notifications
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        // Fetch all notifications (read and unread) and paginate or limit as needed
        $notifications = $user->notifications()->latest()->get();

        return response()->json([
            'unread_count' => $user->unreadNotifications->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark all unread notifications as read for the authenticated user.
     * Corresponds to: POST /api/v1/notifications/mark-read
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read.',
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete a specific notification.
     * Corresponds to: DELETE /api/v1/notifications/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully.'
        ], 204);
    }
}