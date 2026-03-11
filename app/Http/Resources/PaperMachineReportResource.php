<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaperMachineReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'machine_id' => $this->machine_id,
            'operator_id' => $this->operator_id,
            'operator_name' => $this->operator_name,
            'date' => $this->date?->format('Y-m-d'),
            'grup' => $this->grup,
            'steam_kg' => $this->steam_kg,
            'water_l' => $this->water_l,
            'power_mwh' => $this->power_mwh,
            'temperature_c' => $this->temperature_c,
            'total_pm' => $this->total_pm,
            'total_winder' => $this->total_winder,
            'remarks' => $this->remarks,
            'is_locked' => (bool) $this->is_locked,
            'rolls' => $this->rolls ? $this->rolls->map(function ($roll) {
                return [
                    'id' => $roll->id,
                    'report_id' => $roll->report_id,
                    'no' => $roll->no,
                    'jrk_instruction' => $roll->jrk_instruction,
                    'grade' => $roll->grade,
                    'roll_number' => $roll->roll_number,
                    'speed_reel' => $roll->speed_reel,
                    'tonase_roll' => $roll->tonase_roll,
                    'width_cm' => $roll->width_cm,
                    'solid_starch_percent' => $roll->solid_starch_percent,
                    'dry_strength_kg' => $roll->dry_strength_kg,
                    'internal_sizing_kg_per_h' => $roll->internal_sizing_kg_per_h,
                    'floc_l_per_h' => $roll->floc_l_per_h,
                    'coag_l_per_h' => $roll->coag_l_per_h,
                    'yellow_ppm' => $roll->yellow_ppm,
                    'yellow_l_per_h' => $roll->yellow_l_per_h,
                    'red_ppm' => $roll->red_ppm,
                    'red_l_per_h' => $roll->red_l_per_h,
                    'brown_ppm' => $roll->brown_ppm,
                    'brown_l_per_h' => $roll->brown_l_per_h,
                    'external_sizing_kg_per_tp' => $roll->external_sizing_kg_per_tp,
                    'pac_ml_per_m' => $roll->pac_ml_per_m,
                    'is_saved' => $roll->is_saved,
                ];
            })->values() : [],
            'problems' => $this->problems ? $this->problems->map(function ($problem) {
                return [
                    'id' => $problem->id,
                    'report_id' => $problem->report_id,
                    'no' => $problem->no,
                    'description' => $problem->description,
                    'time_start' => $problem->time_start,
                    'time_end' => $problem->time_end,
                    'duration_minutes' => $problem->duration_minutes,
                ];
            })->values() : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}