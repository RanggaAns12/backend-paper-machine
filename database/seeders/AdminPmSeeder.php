<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminPmSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Rangga
        $rangga = User::firstOrCreate(
            ['username' => 'rangga_pm'],
            [
                'name'      => 'Rangga Andriansyah', // Nama lengkap
                'password'  => Hash::make('AdminPm123!'),
                'is_active' => true,
            ]
        );

        // Assign Role 'admin_paper_machine' (Sesuai dengan RoleSeeder)
        $rolePm = Role::findByName('admin_paper_machine', 'web');
        $rangga->assignRole($rolePm);


        // 2. Buat User Operator Tambahan
        $operator = User::firstOrCreate(
            ['username' => 'operator_pm1'],
            [
                'name'      => 'Operator PM 1',
                'password'  => Hash::make('AdminPm123!'),
                'is_active' => true,
            ]
        );
        
        $operator->assignRole($rolePm);
    }
}
