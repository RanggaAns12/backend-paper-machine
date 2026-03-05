<?php

namespace App\Http\Requests\PaperMachine;

use Illuminate\Foundation\Http\FormRequest;

class StorePaperMachineProblemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'no'               => ['required', 'integer'],
            'description'      => ['required', 'string'],
            'time_start'       => ['nullable', 'date_format:H:i'],
            'time_end'         => ['nullable', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
