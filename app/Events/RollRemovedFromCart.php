<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RollRemovedFromCart implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $barcode;

    public function __construct(int $userId, string $barcode)
    {
        $this->userId = $userId;
        $this->barcode = $barcode;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('outbound.user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'roll.removed';
    }
}