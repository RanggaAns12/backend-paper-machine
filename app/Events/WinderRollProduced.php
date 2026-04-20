<?php

namespace App\Events;

use App\Models\WinderLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// 🔥 ShouldBroadcastNow memastikan event langsung dikirim seketika tanpa antrean lambat
class WinderRollProduced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $winderLog;

    public function __construct(WinderLog $winderLog)
    {
        // Membawa data lengkap WinderLog yang baru saja selesai
        $this->winderLog = $winderLog;
    }

    // Tentukan channel (saluran) radio tempat sinyal ini dipancarkan
    public function broadcastOn()
    {
        // Kita buat saluran khusus bernama 'warehouse.inbound'
        // Nanti Angular di Frontend Gudang akan "mendengarkan" saluran ini
        return new PrivateChannel('warehouse.inbound');
    }

    // Nama sinyalnya
    public function broadcastAs()
    {
        return 'roll.ready.for.inbound';
    }
}