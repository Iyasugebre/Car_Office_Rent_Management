<?php

namespace App\Notifications;

use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleMaintenanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $car;
    public $serviceType;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Car $car, string $serviceType, string $message)
    {
        $this->car = $car;
        $this->serviceType = $serviceType;
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
     public function toMail(object $notifiable)
     {
         return (new \Illuminate\Notifications\Messages\MailMessage)
             ->subject('Service Alert: Vehicle ' . $this->car->plate_number)
             ->greeting('Hello,')
             ->line("Vehicle {$this->car->make} {$this->car->model} ({$this->car->plate_number}) is due for {$this->serviceType}.")
             ->line($this->message)
             ->action('View Service Tracker', url('/admin/service-tracker/' . $this->car->id))
             ->line('Regular maintenance ensures fleet safety and performance.');
     }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'vehicle_maintenance',
            'car_id' => $this->car->id,
            'plate_number' => $this->car->plate_number,
            'service_type' => $this->serviceType,
            'message' => $this->message,
            'urgency' => 'warning',
        ];
    }
}
