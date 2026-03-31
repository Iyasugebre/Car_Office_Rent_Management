<?php

namespace App\Notifications;

use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VehicleLegalExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $car;
    public $type; // 'bolo' or 'inspection'
    public $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct(Car $car, string $type, int $daysRemaining)
    {
        $this->car = $car;
        $this->type = $type;
        $this->daysRemaining = $daysRemaining;
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
    public function toMail(object $notifiable)
    {
        $label = $this->type === 'bolo' ? 'Bolo (Annual License)' : 'Vehicle Physical Inspection';
        $content = "Vehicle: {$this->car->plate_number} ({$this->car->make} {$this->car->model})";
        
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('ALERT: ' . strtoupper($this->type) . ' Expiry Notification - ' . $this->car->plate_number)
            ->greeting('Hello ' . ($notifiable->name ?? 'Admin') . ',')
            ->line('This is an automated alert concerning the legal compliance of your fleet.')
            ->line($content)
            ->line("The current {$label} for this vehicle expires in {$this->daysRemaining} days.")
            ->action('Update Legal Records', url('/admin/legal-tracker/' . $this->car->id . '/edit'))
            ->line('Please ensure certificates are renewed and uploaded before the expiry date.')
            ->error();
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $label = $this->type === 'bolo' ? 'Bolo (Annual License)' : 'Vehicle Inspection';
        $message = "The {$label} for vehicle {$this->car->plate_number} will expire in {$this->daysRemaining} days.";
        
        if ($this->daysRemaining <= 0) {
            $message = "CRITICAL: The {$label} for vehicle {$this->car->plate_number} has EXPIRED.";
        }

        return [
            'type' => 'legal_expiry',
            'car_id' => $this->car->id,
            'plate_number' => $this->car->plate_number,
            'legal_type' => $this->type,
            'days_remaining' => $this->daysRemaining,
            'message' => $message,
            'urgency' => $this->daysRemaining <= 7 ? 'danger' : 'warning',
        ];
    }
}
