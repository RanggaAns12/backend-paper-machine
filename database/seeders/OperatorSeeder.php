<?php

namespace Database\Seeders;

use App\Models\Operator;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        $operators = [
            // Operator PM
            ['name' => 'Budi Santoso', 'division' => 'pm', 'is_active' => true],
            ['name' => 'Andi Pratama', 'division' => 'pm', 'is_active' => true],
            ['name' => 'Cipto Mangunkusumo', 'division' => 'pm', 'is_active' => true],
            
            // Operator Winder
            ['name' => 'Dwi Wahyudi', 'division' => 'winder', 'is_active' => true],
            ['name' => 'Eko Prasetyo', 'division' => 'winder', 'is_active' => true],
            ['name' => 'Fajar Nugroho', 'division' => 'winder', 'is_active' => true],
        ];

        foreach ($operators as $op) {
            Operator::updateOrCreate(
                ['name' => $op['name']], 
                [
                    'division'  => $op['division'],
                    'is_active' => $op['is_active']
                ]
            );
        }
    }
}