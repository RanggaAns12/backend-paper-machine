<?php

namespace App\Http\Requests\PaperMachine;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaperMachineReportRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'machine_id' => 'sometimes|integer|exists:machines,id',
            'operator_name' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'grup' => 'sometimes|string|max:50',

            // Disesuaikan dengan kolom di tabel paper_machine_reports
            'steam_kg' => 'sometimes|nullable|numeric|min:0',
            'water_l' => 'sometimes|nullable|numeric|min:0',
            'power_mwh' => 'sometimes|nullable|numeric|min:0',
            'temperature_c' => 'sometimes|nullable|numeric|min:0',

            'total_pm' => 'sometimes|nullable|numeric|min:0',
            'total_winder' => 'sometimes|nullable|numeric|min:0',
            'remarks' => 'sometimes|nullable|string',
            'is_locked' => 'sometimes|boolean',

            'rolls' => 'sometimes|nullable|array',
            'problems' => 'sometimes|nullable|array',
        ];
    }
}
