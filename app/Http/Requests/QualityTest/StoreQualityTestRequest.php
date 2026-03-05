<?php

namespace App\Http\Requests\QualityTest;

use Illuminate\Foundation\Http\FormRequest;

class StoreQualityTestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'report_id'        => ['required', 'integer', 'exists:paper_machine_reports,id'],
            'moisture'         => ['nullable', 'numeric'],
            'tensile_strength' => ['nullable', 'numeric'],
            'brightness'       => ['nullable', 'numeric'],
            'smoothness'       => ['nullable', 'numeric'],
            'result'           => ['required', 'in:pass,fail'],
            'notes'            => ['nullable', 'string'],
            'tested_at'        => ['nullable', 'date'],
        ];
    }
}
