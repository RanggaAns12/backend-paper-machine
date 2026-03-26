<?php

namespace App\Http\Requests\PaperMachine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaperMachineRollRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // FIX FATAL: Tangkap ID Roll dari Payload Body Angular (prioritas utama)
        // Jika tidak ada di body, cek dari route parameter (khusus untuk method PUT/PATCH)
        $rollId = $this->input('id');

        if (!$rollId && in_array($this->method(), ['PUT', 'PATCH'])) {
            $rollId = $this->route('roll') ?? $this->route('paper_machine_roll') ?? $this->route('id');
        }

        $uniqueRollRule = Rule::unique('paper_machine_rolls', 'roll_number');
        
        // Abaikan pengecekan duplikat jika ini adalah proses Update (ID ditemukan)
        if ($rollId) {
            $uniqueRollRule->ignore($rollId);
        }

        return [
            'id'                           => ['nullable', 'integer'], // Tambahkan agar diizinkan masuk payload
            'no'                           => ['required', 'integer', 'min:1', 'max:100'],
            'working_hour'                 => ['nullable', 'string', 'max:100'], 
            'jrk_instruction'              => ['nullable', 'string', 'max:100'],
            'grade'                        => ['nullable', 'string', 'max:100'],
            'roll_number'                  => [
                'required', 
                'string', 
                'max:100',
                $uniqueRollRule
            ],
            'speed_reel'                   => ['nullable', 'numeric'],
            'tonase_roll'                  => ['nullable', 'numeric'],
            'width_cm'                     => ['nullable', 'numeric'],
            'solid_starch_percent'         => ['nullable', 'numeric'],
            'dry_strength_kg'              => ['nullable', 'numeric'],
            'floc_l_per_h'                 => ['nullable', 'numeric'],
            'coag_l_per_h'                 => ['nullable', 'numeric'],
            'brown_ppm'                    => ['nullable', 'numeric'],
            'brown_l_per_h'                => ['nullable', 'numeric'],
            'yellow_ppm'                   => ['nullable', 'numeric'],
            'yellow_l_per_h'               => ['nullable', 'numeric'],
            'red_ppm'                      => ['nullable', 'numeric'],
            'red_l_per_h'                  => ['nullable', 'numeric'],
            'external_sizing_kg_per_tp'    => ['nullable', 'numeric'],
            'pac_ml_per_m'                 => ['nullable', 'numeric'],
        ];
    }
}