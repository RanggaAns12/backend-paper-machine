<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. DATA MASTER (Pondasi Utama)
            RoleSeeder::class,       
            MachineSeeder::class,    
            OperatorSeeder::class,   

            // 2. DATA PENGGUNA (Akun-akun Karyawan)
            SuperadminSeeder::class,
            AdminPmSeeder::class,
            AdminLabSeeder::class,
            AdminWinderSeeder::class,

            // 3. DATA TRANSAKSI / ALUR PABRIK (Efek Domino)
            PaperMachineSeeder::class, // Langkah 1: Mesin PM mencetak Jumbo Roll
            QualityTestSeeder::class,  // Langkah 2: Tim Lab QC mengetes Jumbo Roll tersebut
            WinderLogSeeder::class,    // Langkah 3: Mesin Winder memotong roll yang LULUS QC
        ]);
    }
}