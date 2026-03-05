<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WinderLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'report_id'     => $this->report_id,
            'operator'      => new UserResource($this->whenLoaded('operator')),
            'roll_number'   => $this->roll_number,
            'roll_weight'   => $this->roll_weight,
            'core_diameter' => $this->core_diameter,
            'width'         => $this->width,
            'status'        => $this->status,
            'wound_at'      => $this->wound_at?->toDateTimeString(),
            'created_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}
