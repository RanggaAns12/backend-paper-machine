<?php

namespace App\Http\Requests\WinderLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreWinderLogRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan membuat request ini.
     */
    public function authorize(): bool
    {
        return true; // Asumsinya otorisasi sudah di-handle oleh Middleware/Guard di rute API
    }

    /**
     * Aturan validasi yang harus dipenuhi oleh data yang masuk.
     */
    public function rules(): array
    {
        return [
            'report_id'     => ['required', 'exists:paper_machine_reports,id'],
            'operator_id'   => ['required', 'exists:operators,id'],
            'roll_number'   => ['required', 'string', 'max:255'],
            'roll_weight'   => ['nullable', 'numeric', 'min:0'],
            'core_diameter' => ['nullable', 'numeric', 'min:0'],
            'width'         => ['nullable', 'numeric', 'min:0'],
            'status'        => ['nullable', 'in:pending,done'],
            'wound_at'      => ['nullable', 'date'],
        ];
    }

    /**
     * Pesan error kustom (opsional, agar lebih mudah dibaca oleh Frontend).
     */
    public function messages(): array
    {
        return [
            'report_id.required'   => 'Laporan Paper Machine (Report ID) wajib diisi.',
            'report_id.exists'     => 'Laporan Paper Machine tidak ditemukan di sistem.',
            'operator_id.required' => 'Data Operator wajib diisi.',
            'operator_id.exists'   => 'Operator tidak ditemukan di sistem.',
            'roll_number.required' => 'Nomor Roll wajib diisi.',
        ];
    }
}