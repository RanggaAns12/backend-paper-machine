<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualityTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'report_id'        => $this->report_id,
            'tested_by'        => new UserResource($this->whenLoaded('tester')),
            'moisture'         => $this->moisture,
            'tensile_strength' => $this->tensile_strength,
            'brightness'       => $this->brightness,
            'smoothness'       => $this->smoothness,
            'result'           => $this->result,
            'notes'            => $this->notes,
            'tested_at'        => $this->tested_at?->toDateTimeString(),
            'created_at'       => $this->created_at?->toDateTimeString(),
        ];
    }
}
