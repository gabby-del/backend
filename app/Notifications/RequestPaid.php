<?php

namespace App\Notifications;

use App\Models\PaymentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestPaid extends Notification implements ShouldQueue
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
                    ->subject('Payment Disbursed Successfully')
                    ->greeting("Payment Complete!")
                    ->line("Your payment request (#{$this->paymentRequest->id}: {$this->paymentRequest->title}) for **{$this->paymentRequest->amount}** has been processed and paid.")
                    ->line('The payment was finalized by the Finance Officer.')
                    ->action('View Details', url('/requests/' . $this->paymentRequest->id));
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
            'message' => "Your request #{$this->paymentRequest->id} has been fully paid.",
            'action_url' => '/requests/' . $this->paymentRequest->id,
        ];
    }
}