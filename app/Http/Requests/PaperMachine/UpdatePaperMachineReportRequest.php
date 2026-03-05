<?php

namespace App\Http\Requests\PaperMachine;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaperMachineReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'machine_id'    => ['sometimes', 'integer', 'exists:machines,id'],
            'date'          => ['sometimes', 'date'],
            'grup'          => ['sometimes', 'string', 'max:100'],
            'working_hour'  => ['sometimes', 'numeric', 'min:0'],
            'steam'         => ['sometimes', 'numeric', 'min:0'],
            'water'         => ['sometimes', 'numeric', 'min:0'],
            'kg_per_shift'  => ['sometimes', 'numeric', 'min:0'],
            'l_per_shift'   => ['sometimes', 'numeric', 'min:0'],
            'power'         => ['sometimes', 'numeric', 'min:0'],
            'temperature'   => ['sometimes', 'numeric'],
            'mwh_per_shift' => ['sometimes', 'numeric', 'min:0'],
            'total_pm'      => ['sometimes', 'numeric', 'min:0'],
            'total_winder'  => ['sometimes', 'numeric', 'min:0'],
            'remarks'       => ['nullable', 'string'],
        ];
    }
}
