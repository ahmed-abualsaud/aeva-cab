<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BusinessTripStatus extends Notification implements ShouldQueue
{
    use Queueable;

    protected $trip;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($trip)
    {
        $this->trip = $trip;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'trip_id' => $this->trip['id'],
            'name' => $this->trip['name'],
            'status' => $this->trip['status'],
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'trip_id' => $this->trip['id'],
            'log_id' => $this->trip['log_id'],
            'name' => $this->trip['name'],
            'status' => $this->trip['status'],
        ]);
    }

}
