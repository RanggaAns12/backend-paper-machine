<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaperMachineReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'machine'       => new MachineResource($this->whenLoaded('machine')),
            'operator'      => new UserResource($this->whenLoaded('operator')),
            'date'          => $this->date?->toDateString(),
            'grup'          => $this->grup,
            'working_hour'  => $this->working_hour,
            'steam'         => $this->steam,
            'water'         => $this->water,
            'kg_per_shift'  => $this->kg_per_shift,
            'l_per_shift'   => $this->l_per_shift,
            'power'         => $this->power,
            'temperature'   => $this->temperature,
            'mwh_per_shift' => $this->mwh_per_shift,
            'total_pm'      => $this->total_pm,
            'total_winder'  => $this->total_winder,
            'remarks'       => $this->remarks,
            'rolls'         => PaperMachineRollResource::collection($this->whenLoaded('rolls')),
            'problems'      => PaperMachineProblemResource::collection($this->whenLoaded('problems')),
            'created_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}
