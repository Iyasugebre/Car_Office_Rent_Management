<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $serviceRequest;
    public $status;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(ServiceRequest $serviceRequest, string $status, string $message)
    {
        $this->serviceRequest = $serviceRequest;
        $this->status = $status;
        $this->message = $message;
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
        $subject = match($this->status) {
            'pending' => 'New Maintenance Request: ' . $this->serviceRequest->car->plate_number,
            'approved' => 'Service Request Approved: ' . $this->serviceRequest->car->plate_number,
            'completed' => 'Maintenance Completed: ' . $this->serviceRequest->car->plate_number,
            'rejected' => 'Service Request Rejected: ' . $this->serviceRequest->car->plate_number,
            default => 'Maintenance Update: ' . $this->serviceRequest->car->plate_number,
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello,')
            ->line($this->message)
            ->line("Vehicle: {$this->serviceRequest->car->plate_number} ({$this->serviceRequest->car->make})")
            ->line("Service Type: " . ucfirst($this->serviceRequest->service_type))
            ->action('View Details', url('/admin/services/' . $this->serviceRequest->id))
            ->line('Track the full maintenance lifecycle in the ERP dashboard.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'maintenance_request',
            'request_id' => $this->serviceRequest->id,
            'status' => $this->status,
            'plate_number' => $this->serviceRequest->car->plate_number,
            'message' => $this->message,
            'urgency' => $this->status === 'pending' && $this->serviceRequest->urgency_level === 'critical' ? 'critical' : 'info',
        ];
    }
}
