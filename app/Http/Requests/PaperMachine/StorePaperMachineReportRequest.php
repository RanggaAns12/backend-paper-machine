<?php

namespace App\Http\Requests\PaperMachine;

use Illuminate\Foundation\Http\FormRequest;

class StorePaperMachineReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'machine_id'    => ['required', 'integer', 'exists:machines,id'],
            'operator_name' => ['required', 'string', 'max:255'],
            'date'          => ['required', 'date'],
            'grup'          => ['required', 'string', 'max:100'],
            'working_hour'  => ['required', 'numeric', 'min:0'],
            'steam'         => ['required', 'numeric', 'min:0'],
            'water'         => ['required', 'numeric', 'min:0'],
            'kg_per_shift'  => ['required', 'numeric', 'min:0'],
            'l_per_shift'   => ['required', 'numeric', 'min:0'],
            'power'         => ['required', 'numeric', 'min:0'],
            'temperature'   => ['required', 'numeric'],
            'mwh_per_shift' => ['required', 'numeric', 'min:0'],
            'total_pm'      => ['required', 'numeric', 'min:0'],
            'total_winder'  => ['required', 'numeric', 'min:0'],
            'remarks'       => ['nullable', 'string'],

            'rolls'                                => ['nullable', 'array', 'max:11'],
            'rolls.*.no'                           => ['required', 'integer', 'min:1', 'max:11'],
            'rolls.*.roll_number'                  => ['required', 'string', 'max:100'],
            'rolls.*.speed_reel_wire'              => ['nullable', 'numeric'],
            'rolls.*.tonase_roll'                  => ['nullable', 'numeric'],
            'rolls.*.width_cm'                     => ['nullable', 'numeric'],
            'rolls.*.solid_starch_percent'         => ['nullable', 'numeric'],
            'rolls.*.internal_sizing_kg_per_h'     => ['nullable', 'numeric'],
            'rolls.*.floc_l_per_h'                 => ['nullable', 'numeric'],
            'rolls.*.coag_l_per_h'                 => ['nullable', 'numeric'],
            'rolls.*.yellow_ppm'                   => ['nullable', 'numeric'],
            'rolls.*.yellow_l_per_h'               => ['nullable', 'numeric'],
            'rolls.*.red_ppm'                      => ['nullable', 'numeric'],
            'rolls.*.red_l_per_h'                  => ['nullable', 'numeric'],
            'rolls.*.brown_ppm'                    => ['nullable', 'numeric'],
            'rolls.*.brown_l_per_h'                => ['nullable', 'numeric'],
            'rolls.*.external_sizing_kg_per_tp'    => ['nullable', 'numeric'],
            'rolls.*.pac_ml_per_m'                 => ['nullable', 'numeric'],

            'problems'                     => ['nullable', 'array'],
            'problems.*.no'                => ['required', 'integer'],
            'problems.*.description'       => ['required', 'string'],
            'problems.*.time_start'        => ['nullable', 'date_format:H:i'],
            'problems.*.time_end'          => ['nullable', 'date_format:H:i'],
            'problems.*.duration_minutes'  => ['nullable', 'integer', 'min:0'],
        ];
    }
}
