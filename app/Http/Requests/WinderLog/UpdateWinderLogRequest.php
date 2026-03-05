<?php

namespace App\Http\Requests\WinderLog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWinderLogRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'roll_number'   => ['sometimes', 'string', 'max:100'],
            'roll_weight'   => ['nullable', 'numeric'],
            'core_diameter' => ['nullable', 'numeric'],
            'width'         => ['nullable', 'numeric'],
            'status'        => ['sometimes', 'in:pending,done'],
            'wound_at'      => ['nullable', 'date'],
        ];
    }
}
