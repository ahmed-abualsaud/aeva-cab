<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CabRequestCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $by;
    public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($by, $request)
    {
        $this->by = $by;
        $this->request = $request;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ( $this->by == 'user' ) {
            return new PrivateChannel('Request.Cancelled.'.$this->request->id);
        }

        if ( $this->by == 'driver' ) {
            return new PrivateChannel('App.CapTrip.'.$this->request->id);
        }

        return null;
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
        return ['request' => $this->request];
    }
}
