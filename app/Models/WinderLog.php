<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WinderLog extends Model
{
    // HasFactory ditambahkan sebagai standar Laravel jika nantinya kita butuh fitur Seeder/Testing
    use HasFactory;

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara langsung (Mass Assignment).
     */
    protected $fillable = [
        'report_id', 
        'operator_id', 
        'roll_number',
        'roll_weight', 
        'core_diameter', 
        'width', 
        'status', 
        'wound_at',
    ];

    /**
     * Casts berfungsi untuk mengubah tipe data secara otomatis saat dibaca atau disimpan.
     */
    protected $casts = [
        'roll_weight'   => 'decimal:2',
        'core_diameter' => 'decimal:2',
        'width'         => 'decimal:2',
        'wound_at'      => 'datetime', // Mengubah string tanggal menjadi objek Carbon (mudah dimanipulasi)
    ];

    /**
     * Relasi ke Laporan Paper Machine (Sumber Jumbo Roll).
     * Satu log winder ini adalah hasil dari satu laporan PM tertentu.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(PaperMachineReport::class, 'report_id');
    }

    /**
     * Relasi ke Data Operator Lapangan.
     * ✅ PERBAIKAN: Mengarah ke model Operator, BUKAN User.
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'operator_id');
    }
}