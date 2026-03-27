<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. DATA MASTER (Jalankan paling awal karena tidak bergantung pada tabel lain)
            RoleSeeder::class,       // Wajib pertama agar user bisa punya role
            MachineSeeder::class,    // Wajib jalan sebelum laporan PM dibuat
            OperatorSeeder::class,   // Wajib jalan agar daftar operator ada

            // 2. DATA PENGGUNA (Bergantung pada RoleSeeder)
            SuperadminSeeder::class,
            AdminPmSeeder::class,
            AdminWinderSeeder::class,

            // 3. DATA TRANSAKSI / LAPORAN (Bergantung pada MachineSeeder & OperatorSeeder)
            PaperMachineSeeder::class, 
        ]);
    }
}