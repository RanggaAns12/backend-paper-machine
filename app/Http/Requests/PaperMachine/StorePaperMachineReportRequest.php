<?php

namespace App\Http\Requests\PaperMachine;

use Illuminate\Foundation\Http\FormRequest;

class StorePaperMachineReportRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'machine_id' => 'required|integer|exists:machines,id',
            'operator_name' => 'required|string|max:255',
            'date' => 'required|date',
            'grup' => 'required|string|max:50',
            
            'steam_kg' => 'nullable|numeric|min:0',
            'water_l' => 'nullable|numeric|min:0',
            'power_mwh' => 'nullable|numeric|min:0',
            'temperature_c' => 'nullable|numeric|min:0',
            
            'total_pm' => 'nullable|numeric|min:0',
            'total_winder' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'is_locked' => 'boolean',

            'rolls'                                => ['nullable', 'array', 'max:11'],
            'rolls.*.no'                           => ['required', 'integer', 'min:1', 'max:11'],
            'rolls.*.jrk_instruction'              => ['nullable', 'string', 'max:100'],
            'rolls.*.grade'                        => ['nullable', 'string', 'max:100'],
            'rolls.*.roll_number'                  => ['required', 'string', 'max:100', 'distinct', 'unique:paper_machine_rolls,roll_number'],
            'rolls.*.speed_reel'                   => ['nullable', 'numeric'],
            'rolls.*.tonase_roll'                  => ['nullable', 'numeric'],
            'rolls.*.width_cm'                     => ['nullable', 'numeric'],
            'rolls.*.solid_starch_percent'         => ['nullable', 'numeric'],
            'rolls.*.dry_strength_kg'              => ['nullable', 'numeric'],
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

    public function messages()
    {
        return [
            'operator_name.required' => 'Nama operator wajib diisi.',
            'machine_id.required' => 'Mesin wajib dipilih.',
            'rolls.*.roll_number.unique' => 'Nomor Roll ada yang duplikat/sudah pernah digunakan.',
        ];
    }
}