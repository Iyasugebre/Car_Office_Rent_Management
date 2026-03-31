<?php

namespace App\Notifications;

use App\Models\Agreement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $agreement;
    public $message;
    public $daysUntilDue;

    /**
     * Create a new notification instance.
     */
    public function __construct(Agreement $agreement, string $message, int $daysUntilDue)
    {
        $this->agreement = $agreement;
        $this->message = $message;
        $this->daysUntilDue = $daysUntilDue;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Office Rent Payment Alert: ' . $this->agreement->agreement_id)
            ->greeting('Hello,')
            ->line($this->message)
            ->line("Agreement: {$this->agreement->agreement_id}")
            ->line("Property: {$this->agreement->property_address}")
            ->line("Monthly Rent: {$this->agreement->monthly_rent}")
            ->action('View Agreement', url('/admin/agreements/' . $this->agreement->id))
            ->line('Please process the payment to ensure lease continuity.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rent_payment_due',
            'agreement_id' => $this->agreement->id,
            'agreement_ref' => $this->agreement->agreement_id,
            'message' => $this->message,
            'days_until_due' => $this->daysUntilDue,
            'urgency' => $this->daysUntilDue <= 7 ? 'critical' : 'warning',
        ];
    }
}
