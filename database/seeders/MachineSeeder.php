<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    public function run(): void
    {
        $machines = [
            [
                'name'        => 'Lab Machine 1',
                'type'        => 'lab',
                'status'      => 'active',
                'description' => 'Mesin pengujian kualitas laboratorium',
            ],
            [
                'name'        => 'Paper Machine PM-01',
                'type'        => 'paper_machine',
                'status'      => 'active',
                'description' => 'Mesin produksi kertas utama',
            ],
            [
                'name'        => 'Winder WD-01',
                'type'        => 'winder',
                'status'      => 'active',
                'description' => 'Mesin winder penggulungan kertas',
            ],
        ];

        foreach ($machines as $machine) {
            Machine::firstOrCreate(['name' => $machine['name']], $machine);
        }
    }
}
