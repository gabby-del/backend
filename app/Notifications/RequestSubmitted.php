<?php

namespace App\Notifications;

use App\Models\PaymentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $paymentRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(PaymentRequest $paymentRequest)
    {
        $this->paymentRequest = $paymentRequest;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Use both in-app (database) and email channels
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Payment Request Awaiting Approval')
                    ->line("A new payment request (#{$this->paymentRequest->id}: {$this->paymentRequest->title}) requires your review and approval.")
                    ->line('**Amount:** ' . number_format($this->paymentRequest->amount, 2))
                    ->action('Review Request', url('/requests/' . $this->paymentRequest->id))
                    ->line('Please check the budget allocation before proceeding with approval.');
    }

    /**
     * Get the array representation of the notification (for in-app/database storage).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->paymentRequest->id,
            'title' => $this->paymentRequest->title,
            'amount' => $this->paymentRequest->amount,
            'message' => "Request #{$this->paymentRequest->id} submitted and requires your approval.",
            'action_url' => '/requests/' . $this->paymentRequest->id,
        ];
    }
}
