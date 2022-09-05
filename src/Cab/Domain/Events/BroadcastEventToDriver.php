<?php

namespace Aeva\Cab\Domain\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastEventToDriver implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driver_id;
    public $event_name;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($event_name, $driver_id, $data=null)
    {
        $this->event_name = $event_name;
        $this->driver_id = $driver_id;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->event_name == 'dismiss') {
            return new PrivateChannel('Dismiss.Request.Driver.'.$this->driver_id);
        }

        if ($this->event_name == 'missed') {
            return new PrivateChannel('Missed.Request.Driver.'.$this->driver_id);
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

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['data' => $this->data];
    }
}
