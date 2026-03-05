<?php

namespace App\Http\Requests\PaperMachine;

use Illuminate\Foundation\Http\FormRequest;

class StorePaperMachineRollRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'no'                           => ['required', 'integer', 'min:1', 'max:11'],
            'roll_number'                  => ['required', 'string', 'max:100'],
            'speed_reel_wire'              => ['nullable', 'numeric'],
            'tonase_roll'                  => ['nullable', 'numeric'],
            'width_cm'                     => ['nullable', 'numeric'],
            'solid_starch_percent'         => ['nullable', 'numeric'],
            'internal_sizing_kg_per_h'     => ['nullable', 'numeric'],
            'floc_l_per_h'                 => ['nullable', 'numeric'],
            'coag_l_per_h'                 => ['nullable', 'numeric'],
            'yellow_ppm'                   => ['nullable', 'numeric'],
            'yellow_l_per_h'               => ['nullable', 'numeric'],
            'red_ppm'                      => ['nullable', 'numeric'],
            'red_l_per_h'                  => ['nullable', 'numeric'],
            'black_ppm'                    => ['nullable', 'numeric'],
            'black_l_per_h'                => ['nullable', 'numeric'],
            'external_sizing_kg_per_tp'    => ['nullable', 'numeric'],
            'pac_ml_per_m'                 => ['nullable', 'numeric'],
        ];
    }
}
