<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PaymentRequest;
class RequestApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(PaymentRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     * We specify 'database' for the in-app notification system.
     */
    public function via(object $notifiable): array
    {
       
        return ['database','mail']; 
    }

    /**
     * Get the array representation of the notification.
     * This data is stored as JSON in the 'data' column of the notifications table.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'request_approved',
            'request_id' => $this->request->id,
            'title' => $this->request->title, // Assuming your model has a 'title' field
            'amount' => $this->request->amount,
            'message' => "Your request #{$this->request->id} has been officially approved!",
        ];
    }
}