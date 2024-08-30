<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TurnstileQRScannedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $qrCode;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($qrCode)
    {
        $this->qrCode = $qrCode;
    }

    public function broadcastAs()
    {
        return 'new-arrival';
    }

    public function broadcastWith()
    {
        return [$this->qrCode];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('doorAccessLive');
    }
}
