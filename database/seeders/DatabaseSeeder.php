<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperadminSeeder::class,
            AdminPmSeeder::class,
            AdminLabSeeder::class,
            AdminWinderSeeder::class,
            
            // Tambahkan Seeder User Gudang
            AdminWarehouseSeeder::class,

            MachineSeeder::class,
            OperatorSeeder::class,
            PaperMachineSeeder::class,
            QualityTestSeeder::class,
            WinderLogSeeder::class,
            
            // Tambahkan Seeder Transaksi Gudang di paling bawah
            FinishedGoodSeeder::class,
            DeliveryOrderSeeder::class,
        ]);
    }
}