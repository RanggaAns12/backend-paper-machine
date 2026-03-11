<?php

namespace App\Http\Requests\PaperMachine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaperMachineRollRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Supaya saat Update, ID roll yang sedang diupdate diabaikan dari pengecekan unique
        $rollId = $this->route('id');

        return [
            'no'                           => ['required', 'integer', 'min:1', 'max:11'],
            'jrk_instruction'              => ['nullable', 'string', 'max:100'],
            'grade'                        => ['nullable', 'string', 'max:100'],
            'roll_number'                  => [
                'required', 
                'string', 
                'max:100',
                Rule::unique('paper_machine_rolls', 'roll_number')->ignore($rollId)
            ],
            'speed_reel'                   => ['nullable', 'numeric'],
            'tonase_roll'                  => ['nullable', 'numeric'],
            'width_cm'                     => ['nullable', 'numeric'],
            'solid_starch_percent'         => ['nullable', 'numeric'],
            'dry_strength_kg'              => ['nullable', 'numeric'],
            'internal_sizing_kg_per_h'     => ['nullable', 'numeric'],
            'floc_l_per_h'                 => ['nullable', 'numeric'],
            'coag_l_per_h'                 => ['nullable', 'numeric'],
            'yellow_ppm'                   => ['nullable', 'numeric'],
            'yellow_l_per_h'               => ['nullable', 'numeric'],
            'red_ppm'                      => ['nullable', 'numeric'],
            'red_l_per_h'                  => ['nullable', 'numeric'],
            'brown_ppm'                    => ['nullable', 'numeric'],
            'brown_l_per_h'                => ['nullable', 'numeric'],
            'external_sizing_kg_per_tp'    => ['nullable', 'numeric'],
            'pac_ml_per_m'                 => ['nullable', 'numeric'],
        ];
    }
}