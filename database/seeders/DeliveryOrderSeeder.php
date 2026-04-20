<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryOrder;
use App\Models\User;

class DeliveryOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Cari user admin gudang berdasarkan username (karena kita tidak pakai kolom role di tabel users)
        $admin = User::where('username', 'admin_gudang')->first();
        
        // Jika ketemu pakai ID-nya, jika tidak (untuk jaga-jaga) pakai ID 1
        $adminId = $admin ? $admin->id : 1;

        DeliveryOrder::firstOrCreate(
            ['do_number' => 'DO-' . date('Ymd') . '-0001'],
            [
                'date'          => date('Y-m-d'),
                'customer_name' => 'PT. Kemasan Nusantara',
                'truck_plate'   => 'BK 8899 XYZ',
                'driver_name'   => 'Joko Tarbiah',
                'total_tonase'  => 0,
                'created_by'    => $adminId,
                'status'        => 'draft'
            ]
        );

        $this->command->info('✅ DeliveryOrderSeeder: Draft Surat Jalan berhasil dibuat.');
    }
}