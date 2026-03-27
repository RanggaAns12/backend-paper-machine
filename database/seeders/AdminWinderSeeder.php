<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminWinderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin Winder Utama
        $adminWinder = User::firstOrCreate(
            ['username' => 'admin_winder'],
            [
                'name'      => 'Rangga Andrian Syahputra', // Nama lengkap
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // Assign Role 'admin_winder' (Sesuai dengan Role yang ada di database)
        $roleWinder = Role::findByName('admin_winder', 'web');
        $adminWinder->assignRole($roleWinder);


        // 2. Buat User Operator Winder Tambahan
        $operatorWinder = User::firstOrCreate(
            ['username' => 'operator_winder1'],
            [
                'name'      => 'Operator Winder 1',
                'password'  => Hash::make('AdminWinder123!'),
                'is_active' => true,
            ]
        );
        
        $operatorWinder->assignRole($roleWinder);
    }
}