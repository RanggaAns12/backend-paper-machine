<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Pastikan menambahkan implements ShouldBroadcastNow
class RollScannedToCart implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $rollData;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, array $rollData)
    {
        $this->userId = $userId;
        $this->rollData = $rollData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Kita gunakan PrivateChannel agar hanya akun yang sama yang bisa melihat datanya
        return [
            new PrivateChannel('outbound.user.' . $this->userId),
        ];
    }

    /**
     * Nama event yang akan ditangkap oleh Angular nanti
     */
    public function broadcastAs(): string
    {
        return 'roll.scanned';
    }
}