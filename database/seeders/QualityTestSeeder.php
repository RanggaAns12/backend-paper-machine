<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaperMachineRoll;
use App\Models\QualityTest;
use App\Models\User;

class QualityTestSeeder extends Seeder
{
    public function run(): void
    {
        $adminLab = User::where('username', 'admin_lab')->first();
        
        if (!$adminLab) {
            $this->command->warn('Akun Admin Lab belum ada. Silakan jalankan AdminLabSeeder dulu.');
            return;
        }

        // Ambil Jumbo Roll yang laporannya sudah di-lock oleh PM dan status QC masih pending
        $rolls = PaperMachineRoll::whereHas('report', function($query) {
            $query->where('is_locked', true);
        })->where('qc_status', 'pending')->take(30)->get(); 

        if ($rolls->isEmpty()) {
            $this->command->warn('TIDAK ADA DATA! Pastikan PaperMachineSeeder sudah membuat data Jumbo Roll.');
            return;
        }

        $statuses = ['PASS', 'PASS', 'PASS', 'PASS', 'PASS', 'DOWNGRADE', 'REJECT'];

        foreach ($rolls as $roll) {
            $status = $statuses[array_rand($statuses)];
            
            $notes = null;
            if ($status === 'REJECT') $notes = 'Kertas sobek di bagian tengah dan warna tidak sesuai standar.';
            if ($status === 'DOWNGRADE') $notes = 'Nilai BW (GSM) dan RCT sedikit di bawah standar Testliner 150.';

            QualityTest::create([
                'paper_machine_roll_id' => $roll->id,
                'tested_by'             => $adminLab->id,
                'shift'                 => rand(1, 3),
                'thickness'             => rand(310, 325) / 1000, 
                'bw'                    => rand(145, 155) + (rand(0, 99) / 100), 
                'rct'                   => rand(30, 35) + (rand(0, 99) / 100),   
                'bursting'              => rand(40, 50) / 10,     
                'moisture'              => rand(7, 9) + (rand(0, 99) / 100),     
                'cobb_top'              => rand(230, 250),
                'cobb_bottom'           => rand(235, 255),
                'plybonding'            => ($status === 'REJECT') ? 'TIDAK' : 'BAIK',
                'warna'                 => ($status === 'REJECT') ? 'TIDAK' : 'SESUAI',
                'status'                => $status,
                'notes'                 => $notes,
            ]);

            // ✅ FIX UTAMA: Penerjemah kata 'pass' menjadi 'passed' untuk tabel PM
            $pmStatus = strtolower($status);
            if ($pmStatus === 'pass') {
                $pmStatus = 'passed';
            }

            // Update status di Jumbo Roll dengan kata yang benar
            $roll->update(['qc_status' => $pmStatus]);
        }

        $this->command->info('✅ Seeder QC Sukses! ' . $rolls->count() . ' Jumbo Roll telah dites dengan parameter baru.');
    }
}