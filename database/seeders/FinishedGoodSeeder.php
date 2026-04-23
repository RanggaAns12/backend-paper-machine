<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinishedGood;
use App\Models\WinderLog;

class FinishedGoodSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 Tambahkan with('paperMachineRoll') agar kita bisa ambil grade-nya
        $winderLogs = WinderLog::with('paperMachineRoll')
            ->where('status', 'done')
            ->whereDoesntHave('finishedGood')
            ->take(3)
            ->get();

        if ($winderLogs->isEmpty()) {
            $this->command->warn('⚠️ FinishedGoodSeeder: Tidak ada data WinderLog yang berstatus "done". Pastikan WinderLogSeeder sudah dijalankan lebih dulu.');
            return;
        }

        foreach ($winderLogs as $log) {
            // Ambil grade dari PaperMachineRoll asal, jika tidak ada set default misalnya 'Grade A'
            $grade = $log->paperMachineRoll ? $log->paperMachineRoll->grade : 'Grade A';

            FinishedGood::firstOrCreate(
                ['winder_log_id' => $log->id],
                [
                    'roll_number'    => $log->roll_number,
                    'roll_weight'    => $log->roll_weight,
                    'width'          => $log->width,
                    'core_diameter'  => $log->core_diameter,
                    'grade'          => $grade, // ✅ Tambahkan kolom grade
                    // ❌ location_block dan location_line dihapus
                    'status'         => 'in_stock'
                ]
            );
        }

        $this->command->info('✅ FinishedGoodSeeder: Data stok awal gudang berhasil diisi.');
    }
}