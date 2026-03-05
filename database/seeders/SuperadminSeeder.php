<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('SuperAdmin123!'),
                'is_active' => true,
            ]
        );

        $role = Role::findByName('superadmin', 'web');
        $user->assignRole($role);
    }
}
