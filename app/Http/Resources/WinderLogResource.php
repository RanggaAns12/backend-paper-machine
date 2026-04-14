<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WinderLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'roll_number'           => $this->roll_number,
            'roll_weight'           => $this->roll_weight,
            'core_diameter'         => $this->core_diameter,
            'width'                 => $this->width,
            'status'                => $this->status,
            'wound_at'              => $this->wound_at ? $this->wound_at->format('Y-m-d H:i:s') : null,
            'operator'              => $this->whenLoaded('operator', function () {
                return [
                    'id'   => $this->operator->id,
                    'name' => $this->operator->name, // Sesuaikan dengan kolom nama di tabel operators mas
                ];
            }),
            'paper_machine_roll'    => $this->whenLoaded('paperMachineRoll', function () {
                return [
                    'id'          => $this->paperMachineRoll->id,
                    'roll_number' => $this->paperMachineRoll->roll_number,
                    'tonase_roll' => $this->paperMachineRoll->tonase_roll,
                ];
            }),
            'created_at'            => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}