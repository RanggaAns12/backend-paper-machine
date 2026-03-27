<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WinderLogResource extends JsonResource
{
    /**
     * Mengubah resource (data dari database) ke dalam bentuk array (JSON).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'report_id'     => $this->report_id,
            'operator_id'   => $this->operator_id,
            
            // Mengambil nama operator dari relasi (jika direlasikan/di-load)
            'operator_name' => $this->whenLoaded('operator', function () {
                return $this->operator->name; 
            }),
            
            // Mengambil info laporan PM dari relasi (jika direlasikan/di-load)
            'report_info'   => $this->whenLoaded('report', function () {
                return [
                    'date' => $this->report->date,
                    'grup' => $this->report->grup,
                ];
            }),

            'roll_number'   => $this->roll_number,
            'roll_weight'   => $this->roll_weight,
            'core_diameter' => $this->core_diameter,
            'width'         => $this->width,
            'status'        => $this->status,
            
            // Memformat tanggal agar rapi saat dibaca oleh Angular
            'wound_at'      => $this->wound_at ? $this->wound_at->format('Y-m-d H:i:s') : null,
            'created_at'    => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'    => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}