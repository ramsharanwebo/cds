<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class GenericEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $subject;
    protected $route;
    protected $method;
    protected $descrition;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $route, array $method, Request $request)
    {
        $this->subject = $subject;
        $this->route = $route;
        $this->method = $method;
        $this->descrition = $request->all();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
