<?php

namespace App\Http\Requests\WinderLog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWinderLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Menggunakan $this->route('winder_log') untuk mengecualikan ID saat ini dari validasi unique
        return [
            'paper_machine_roll_id' => ['sometimes', 'exists:paper_machine_rolls,id'],
            'operator_id'           => ['sometimes', 'exists:operators,id'],
            'roll_number'           => ['sometimes', 'string', 'unique:winder_logs,roll_number,' . $this->route('winder_log')],
            'roll_weight'           => ['nullable', 'numeric', 'min:0'],
            'core_diameter'         => ['nullable', 'numeric', 'min:0'],
            'width'                 => ['nullable', 'numeric', 'min:0'],
            'status'                => ['sometimes', 'in:pending,done'],
            'wound_at'              => ['nullable', 'date'],
        ];
    }
}