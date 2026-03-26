<?php

namespace Database\Seeders;

use App\Models\Operator;
use App\Models\User;
use App\Models\PaperMachineReport;
use App\Models\PaperMachineRoll;
use App\Models\PaperMachineProblem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PaperMachineSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cari user yang akan bertindak sebagai pembuat laporan
        $user = User::first();
        $userId = $user ? $user->id : 1;

        // 2. Ambil semua data operator yang aktif
        $operators = Operator::where('is_active', true)->get();

        if ($operators->isEmpty()) {
            $this->command->warn('Tidak ada data operator. Silakan jalankan OperatorSeeder terlebih dahulu.');
            return;
        }

        // Variabel Pilihan Acak
        $shifts = ['A', 'B', 'C'];
        $grades = ['Testliner 150', 'Testliner 125', 'Kraftliner 150', 'Kraftliner 125', 'Medium 110'];
        $jamKerjaShift = ['08:00 - 16:00', '16:00 - 00:00', '00:00 - 08:00'];
        $alasanDowntime = [
            'Web break di area press',
            'Pembersihan wire',
            'Ganti felt',
            'Masalah pada pompa vakum',
            'Motor penggerak trip',
            'Kertas putus di calender',
            'Pembersihan headbox'
        ];

        $jumlahHari = 5; 
        
        // ✅ FIX: Buat Global Counter untuk Serial Number Roll agar 100% Unik
        $globalRollCounter = 1; 

        // 3. Looping untuk setiap Operator
        foreach ($operators as $operator) {
            
            // 4. Looping untuk setiap Tanggal (5 hari ke belakang)
            for ($hari = 0; $hari < $jumlahHari; $hari++) {
                $date = Carbon::today()->subDays($hari);
                
                // 5. Buat 10 Laporan per tanggal untuk operator ini
                for ($i = 1; $i <= 10; $i++) {
                    
                    $grup = $shifts[array_rand($shifts)];
                    $reportWorkingHour = $jamKerjaShift[array_rand($jamKerjaShift)];

                    // Simpan Header Laporan
                    $report = PaperMachineReport::create([
                        'machine_id'    => 1,
                        'operator_id'   => $userId, 
                        'operator_name' => $operator->name, 
                        'date'          => $date->toDateString(),
                        'grup'          => $grup,
                        'steam_kg'      => rand(1400, 1600) + (rand(0, 99) / 100),
                        'water_l'       => rand(1900, 2100) + (rand(0, 99) / 100),
                        'power_mwh'     => rand(10, 15) + (rand(0, 9) / 10),
                        'temperature_c' => rand(80, 90) + (rand(0, 9) / 10),
                        'total_pm'      => 0, 
                        'total_winder'  => 0,
                        'remarks'       => "Laporan ke-{$i} pada " . $date->format('d M Y') . " oleh {$operator->name}.",
                        'is_locked'     => rand(0, 1) == 1, 
                    ]);

                    $totalTonase = 0;
                    $jumlahRoll = 10; 

                    // 6. Buat 10 Baris Roll
                    for ($r = 1; $r <= $jumlahRoll; $r++) {
                        $tonase = rand(1400, 1600) + (rand(0, 99) / 100);
                        $totalTonase += $tonase;

                        // ✅ FIX: Gunakan Global Counter + str_pad agar berformat 0001, 0002, 0003 dst.
                        $uniqueRollNumber = 'R-' . $date->format('Ymd') . '-' . str_pad($globalRollCounter, 4, '0', STR_PAD_LEFT);
                        $globalRollCounter++; // Naikkan nilai counter

                        PaperMachineRoll::create([
                            'report_id'                 => $report->id,
                            'no'                        => $r,
                            'working_hour'              => $reportWorkingHour, 
                            'jrk_instruction'           => 'JRK-00' . rand(1, 5),
                            'grade'                     => $grades[array_rand($grades)],
                            'roll_number'               => $uniqueRollNumber,
                            'speed_reel'                => rand(340, 390) + (rand(0, 99) / 100),
                            'tonase_roll'               => $tonase,
                            'width_cm'                  => rand(240, 250) + (rand(0, 99) / 100),
                            'solid_starch_percent'      => rand(40, 50) / 10,
                            'dry_strength_kg'           => rand(100, 130) / 10,
                            'floc_l_per_h'              => rand(20, 30) / 10,
                            'coag_l_per_h'              => rand(25, 35) / 10,
                            
                            'brown_ppm'                 => rand(100, 150) / 10,
                            'brown_l_per_h'             => rand(10, 20) / 10,
                            'yellow_ppm'                => rand(30, 60) / 10,
                            'yellow_l_per_h'            => rand(5, 10) / 10,
                            'red_ppm'                   => rand(10, 30) / 10,
                            'red_l_per_h'               => rand(2, 6) / 10,
                            
                            'external_sizing_kg_per_tp' => rand(50, 60) / 10,
                            'pac_ml_per_m'              => rand(90, 110),
                            'is_saved'                  => true,
                        ]);
                    }

                    // 7. Update total tonase PM & Winder
                    $report->update([
                        'total_pm'     => $totalTonase,
                        'total_winder' => $totalTonase - rand(10, 50)
                    ]);

                    // 8. Buat Data Downtime / Problem Acak
                    $jumlahDowntime = rand(0, 2);
                    
                    for ($p = 1; $p <= $jumlahDowntime; $p++) {
                        $startHour = rand(8, 14);
                        $startMin = rand(0, 59);
                        $duration = rand(15, 90); 

                        $startTime = Carbon::createFromTime($startHour, $startMin);
                        $endTime = (clone $startTime)->addMinutes($duration);

                        PaperMachineProblem::create([
                            'report_id'        => $report->id,
                            'no'               => $p,
                            'description'      => $alasanDowntime[array_rand($alasanDowntime)],
                            'time_start'       => $startTime->format('H:i'),
                            'time_end'         => $endTime->format('H:i'),
                            'duration_minutes' => $duration,
                        ]);
                    }
                }
            }
        }
    }
}