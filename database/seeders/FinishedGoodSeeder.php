<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinishedGood;
use App\Models\WinderLog;

class FinishedGoodSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil maksimal 3 roll dari winder yang sudah 'done' tapi belum masuk gudang
        $winderLogs = WinderLog::where('status', 'done')
            ->whereDoesntHave('finishedGood')
            ->take(3)
            ->get();

        if ($winderLogs->isEmpty()) {
            $this->command->warn('⚠️ FinishedGoodSeeder: Tidak ada data WinderLog yang berstatus "done". Pastikan WinderLogSeeder sudah dijalankan lebih dulu.');
            return;
        }

        foreach ($winderLogs as $log) {
            FinishedGood::firstOrCreate(
                ['winder_log_id' => $log->id],
                [
                    'roll_number'    => $log->roll_number,
                    'roll_weight'    => $log->roll_weight,
                    'width'          => $log->width,
                    'core_diameter'  => $log->core_diameter,
                    'location_block' => 'A',
                    'location_line'  => '01',
                    'status'         => 'in_stock'
                ]
            );
        }

        $this->command->info('✅ FinishedGoodSeeder: Data stok awal gudang berhasil diisi.');
    }
}