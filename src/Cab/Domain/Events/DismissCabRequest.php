<?php

namespace Aeva\Cab\Domain\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DismissCabRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $drivers_ids;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($drivers_ids)
    {
        $this->drivers_ids = $drivers_ids;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        foreach ($this->drivers_ids as $driver_id) {
            $channels[] = new PrivateChannel('Dismiss.Request.Driver.'.$driver_id);  
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'client-cap.trip.status';
    }
}
