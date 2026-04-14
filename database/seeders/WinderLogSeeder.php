<?php

namespace Database\Seeders;

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

        // 2. 🔥 PERBAIKAN (THE GATEKEEPER): Hanya ambil Jumbo Roll yang LULUS QC (Passed / Downgrade)
        $pmRolls = PaperMachineRoll::whereIn('qc_status', ['passed', 'downgrade'])
                    ->inRandomOrder()
                    ->take(100) 
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

            // Format Nomor Roll Winder (Cth: WD-20260411-0001)
            $uniqueWinderNumber = 'WD-' . $winderDate->format('Ymd') . '-' . str_pad($globalWinderCounter, 4, '0', STR_PAD_LEFT);
            $globalWinderCounter++;

            // Simulasi berat winder (Jumbo Roll PM dipotong sedikit ujungnya / susut pinggir)
            $beratWinder = $pmRoll->tonase_roll - rand(5, 15);

            WinderLog::create([
                'paper_machine_roll_id' => $pmRoll->id,
                'operator_id'           => $operator->id,
                'roll_number'           => $uniqueWinderNumber,
                'roll_weight'           => $beratWinder,
                'core_diameter'         => rand(7, 10),     // Ukuran core standar
                'width'                 => rand(240, 245),  // Lebar setelah di-trim
                'status'                => rand(1, 10) <= 8 ? 'done' : 'pending', // 80% selesai, 20% pending
                'created_at'            => $winderDate,
                'updated_at'            => $winderDate,
            ]);
        }

        $this->command->info('✅ Seeder Winder Sukses! ' . $pmRolls->count() . ' Jumbo Roll Lulus QC telah dipotong.');
    }
}