<?php

namespace Database\Seeders;

use App\Models\Operator;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        $operators = [
            ['name' => 'Budi Santoso', 'is_active' => true],
            ['name' => 'Andi Pratama', 'is_active' => true],
            ['name' => 'Cipto Mangunkusumo', 'is_active' => true],
            ['name' => 'Dwi Wahyudi', 'is_active' => true],
        ];

        foreach ($operators as $op) {
            // Gunakan updateOrCreate agar tidak error duplikat jika di-seed ulang
            Operator::updateOrCreate(
                ['name' => $op['name']], 
                ['is_active' => $op['is_active']]
            );
        }
    }
}