<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminWarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. PASTIKAN ROLE DIBUAT DULU (Anti-Error)
        $roleWarehouse = Role::firstOrCreate([
            'name' => 'admin_warehouse', 
            'guard_name' => 'web'
        ]);

        // 2. Buat User Admin Gudang Utama
        $adminGudang = User::firstOrCreate(
            ['username' => 'admin_gudang'],
            [
                'name'      => 'Admin Gudang (Warehouse)',
                'password'  => Hash::make('AdminGudang123!'),
                'is_active' => true,
            ]
        );

        // Assign Role
        $adminGudang->assignRole($roleWarehouse);


        // 3. Buat User Operator Gudang Tambahan
        $operatorGudang = User::firstOrCreate(
            ['username' => 'operator_gudang1'],
            [
                'name'      => 'Operator Gudang 1',
                'password'  => Hash::make('AdminGudang123!'),
                'is_active' => true,
            ]
        );
        
        // Assign Role
        $operatorGudang->assignRole($roleWarehouse);
        
        $this->command->info('✅ AdminWarehouseSeeder: Role berhasil disiapkan, Akun Admin dan Operator Gudang berhasil dibuat.');
    }
}