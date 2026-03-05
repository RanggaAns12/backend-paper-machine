<?php

namespace App\Http\Requests\WinderLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreWinderLogRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'report_id'     => ['required', 'integer', 'exists:paper_machine_reports,id'],
            'roll_number'   => ['required', 'string', 'max:100'],
            'roll_weight'   => ['nullable', 'numeric'],
            'core_diameter' => ['nullable', 'numeric'],
            'width'         => ['nullable', 'numeric'],
            'status'        => ['sometimes', 'in:pending,done'],
            'wound_at'      => ['nullable', 'date'],
        ];
    }
}
