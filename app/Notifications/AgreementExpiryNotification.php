<?php

namespace App\Notifications;

use App\Models\Agreement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AgreementExpiryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Agreement $agreement,
        public int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Action Required: Agreement Expiring Soon - ' . $this->agreement->agreement_id)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is an automated alert. The lease agreement ' . $this->agreement->agreement_id . ' is set to expire in ' . $this->daysRemaining . ' days.')
            ->line('Property Address: ' . $this->agreement->property_address)
            ->line('Expiration Date: ' . \Carbon\Carbon::parse($this->agreement->end_date)->format('F d, Y'))
            ->action('Review & Renew Agreement', url('/admin/agreements/' . $this->agreement->id))
            ->line('Please initiate the renewal or termination process before the expiration date.');
    }

    public function toArray(object $notifiable): array
    {
        $urgency = match(true) {
            $this->daysRemaining <= 30 => 'critical',
            $this->daysRemaining <= 60 => 'warning',
            default                    => 'notice',
        };

        return [
            'type'             => 'agreement_expiry',
            'urgency'          => $urgency,
            'agreement_id'     => $this->agreement->id,
            'agreement_ref'    => $this->agreement->agreement_id,
            'branch_name'      => $this->agreement->branchRequest->branch_name ?? 'N/A',
            'end_date'         => $this->agreement->end_date,
            'days_remaining'   => $this->daysRemaining,
            'message'          => "Agreement {$this->agreement->agreement_id} expires in {$this->daysRemaining} days ({$this->agreement->end_date}).",
        ];
    }
}
