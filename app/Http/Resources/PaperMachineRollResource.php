<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaperMachineRollResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'report_id'                 => $this->report_id,
            'no'                        => $this->no,
            'roll_number'               => $this->roll_number,
            'speed_reel_wire'           => $this->speed_reel_wire,
            'tonase_roll'               => $this->tonase_roll,
            'width_cm'                  => $this->width_cm,
            'solid_starch_percent'      => $this->solid_starch_percent,
            'internal_sizing_kg_per_h'  => $this->internal_sizing_kg_per_h,
            'floc_l_per_h'              => $this->floc_l_per_h,
            'coag_l_per_h'              => $this->coag_l_per_h,
            'yellow_ppm'                => $this->yellow_ppm,
            'yellow_l_per_h'            => $this->yellow_l_per_h,
            'red_ppm'                   => $this->red_ppm,
            'red_l_per_h'               => $this->red_l_per_h,
            'black_ppm'                 => $this->black_ppm,
            'black_l_per_h'             => $this->black_l_per_h,
            'external_sizing_kg_per_tp' => $this->external_sizing_kg_per_tp,
            'pac_ml_per_m'              => $this->pac_ml_per_m,
        ];
    }
}
