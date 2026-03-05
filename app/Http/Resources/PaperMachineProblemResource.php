<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaperMachineProblemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'report_id'        => $this->report_id,
            'no'               => $this->no,
            'description'      => $this->description,
            'time_start'       => $this->time_start,
            'time_end'         => $this->time_end,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}
