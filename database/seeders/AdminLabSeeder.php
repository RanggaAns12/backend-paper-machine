<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminLabSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Kepala Lab / Admin Lab Utama
        $adminLab = User::firstOrCreate(
            ['username' => 'admin_lab'],
            [
                'name'      => 'Kepala QC Lab',
                'password'  => Hash::make('password'), // Sesuaikan password jika perlu
                'is_active' => true,
            ]
        );

        // Assign Role 'admin_lab' (Sesuai dengan nama role di RoleSeeder mas)
        $roleLab = Role::findByName('admin_lab', 'web');
        $adminLab->assignRole($roleLab);


        // 2. Buat Akun Staff / Penguji Lab Tambahan (Opsional)
        $staffLab = User::firstOrCreate(
            ['username' => 'staff_lab1'],
            [
                'name'      => 'Staff Analis QC 1',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );
        
        $staffLab->assignRole($roleLab);
    }
}