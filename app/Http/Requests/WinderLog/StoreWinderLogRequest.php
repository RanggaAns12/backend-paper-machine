<?php

namespace App\Http\Requests\WinderLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreWinderLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'paper_machine_roll_id' => ['required', 'exists:paper_machine_rolls,id'],
            'operator_id'           => ['required', 'exists:operators,id'],
            'roll_number'           => ['required', 'string', 'unique:winder_logs,roll_number'],
            'roll_weight'           => ['nullable', 'numeric', 'min:0'],
            'core_diameter'         => ['nullable', 'numeric', 'min:0'],
            'width'                 => ['nullable', 'numeric', 'min:0'],
            'status'                => ['nullable', 'in:pending,done'],
            'wound_at'              => ['nullable', 'date'],
            
            // ✅ TAMBAHAN BARU: Izinkan po_number masuk dan pastikan PO-nya valid (ada di database)
            'po_number'             => ['nullable', 'string', 'exists:pre_orders,po_number'],
        ];
    }
}