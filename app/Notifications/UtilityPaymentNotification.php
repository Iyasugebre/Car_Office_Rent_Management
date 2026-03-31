<?php

namespace App\Notifications;

use App\Models\BranchUtility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UtilityPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $utility;
    public $daysUntilDue;

    /**
     * Create a new notification instance.
     */
    public function __construct(BranchUtility $utility, int $daysUntilDue)
    {
        $this->utility = $utility;
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
        $type = ucfirst($this->utility->utility_type);
        $subject = "Utility Payment Alert: {$type} for Branch " . ($this->utility->office->name ?? 'Office');
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello Finance Team,')
            ->line("This is a reminder that the **{$type}** bill for branch **" . ($this->utility->office->name ?? 'Office') . "** is due.")
            ->line("Provider: {$this->utility->provider}")
            ->line("Account Number: {$this->utility->account_number}")
            ->line("Due Date: " . ($this->utility->next_due_at ? $this->utility->next_due_at->format('M d, Y') : 'N/A'))
            ->action('View Utilities', url('/admin/branch-utilities'))
            ->line('Please process the payment to avoid service interruption.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'utility_payment_due',
            'utility_id' => $this->utility->id,
            'office_name' => $this->utility->office->name ?? 'Office',
            'utility_type' => $this->utility->utility_type,
            'days_until_due' => $this->daysUntilDue,
            'message' => "{$this->utility->utility_type} bill for " . ($this->utility->office->name ?? 'Office') . " is due in {$this->daysUntilDue} days.",
            'urgency' => $this->daysUntilDue <= 7 ? 'critical' : 'warning',
        ];
    }
}
