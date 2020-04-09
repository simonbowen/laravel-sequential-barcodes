<?php

namespace SimonBowen\Barcode\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BarcodeCounterUpdated
{
    use Dispatchable;

    public string $counter;

    public function __construct(string $counter)
    {
        $this->counter = $counter;
    }
}
