<?php

namespace Database\Seeders;

use App\Models\FinishedGood;
use App\Models\Operator;
use App\Models\PaperMachineRoll;
use App\Models\WinderLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WinderLogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil HANYA operator dari divisi Winder
        $winderOperators = Operator::where('division', 'winder')->where('is_active', true)->get();

        if ($winderOperators->isEmpty()) {
            $this->command->warn('Tidak ada data operator Winder. Skip WinderLogSeeder.');
            return;
        }

        // 2. 🔥 PERBAIKAN: Hanya ambil Jumbo Roll yang LULUS QC (Passed / Downgrade) dan tonasenya masih ada
        $pmRolls = PaperMachineRoll::whereIn('qc_status', ['passed', 'downgrade'])
                    ->where('tonase_roll', '>', 0)
                    ->inRandomOrder()
                    ->take(50) // Ambil 50 Jumbo Roll untuk di-seed
                    ->get();
        
        if ($pmRolls->isEmpty()) {
            $this->command->warn('Tidak ada data PM Roll yang lulus QC. Jalankan QualityTestSeeder terlebih dahulu.');
            return;
        }

        $globalWinderCounter = 1;

        // 3. Looping untuk membuat Log Winder berdasarkan Jumbo Roll
        foreach ($pmRolls as $pmRoll) {
            
            // Pilih operator winder secara acak
            $operator = $winderOperators->random();
            
            // Waktu potong winder biasanya beberapa jam setelah PM roll selesai
            $winderDate = Carbon::parse($pmRoll->created_at)->addHours(rand(1, 5));

            // Format Nomor Roll Winder (Cth: WD-20260424-0001)
            $uniqueWinderNumber = 'WD-' . $winderDate->format('Ymd') . '-' . str_pad($globalWinderCounter, 4, '0', STR_PAD_LEFT);
            $globalWinderCounter++;

            // Simulasi berat winder (Buat berat acak yang lebih kecil dari Jumbo Roll)
            // Misalnya satu Jumbo Roll dipotong jadi roll kecil seberat 200 - 500 kg
            $beratWinder = rand(200, min(500, floor($pmRoll->tonase_roll)));

            // Buat Winder Log
            $log = WinderLog::create([
                'paper_machine_roll_id' => $pmRoll->id,
                'operator_id'           => $operator->id,
                'roll_number'           => $uniqueWinderNumber,
                'roll_weight'           => $beratWinder,
                'core_diameter'         => rand(7, 10),     // Ukuran core standar
                'width'                 => rand(240, 245),  // Lebar setelah di-trim
                'status'                => 'done',          // 🔥 Selalu 'done'
                'created_at'            => $winderDate,
                'updated_at'            => $winderDate,
            ]);

            // 🔥 Kurangi Tonase Jumbo Roll secara real-time
            $pmRoll->tonase_roll -= $beratWinder;
            $pmRoll->save();

            // 🔥 Otomatis masukkan ke Gudang (Finished Goods)
            FinishedGood::create([
                'winder_log_id' => $log->id,
                'roll_number'   => $log->roll_number,
                'roll_weight'   => $log->roll_weight,
                'width'         => $log->width,
                'core_diameter' => $log->core_diameter,
                'grade'         => $pmRoll->grade ?? 'N/A',
                'status'        => 'in_stock',
                'created_at'    => $winderDate,
                'updated_at'    => $winderDate,
            ]);
        }

        $this->command->info('✅ Seeder Winder & Gudang Sukses! ' . $pmRolls->count() . ' Roll kecil berhasil dipotong dan masuk gudang.');
    }
}