<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityTest extends Model
{
    use HasFactory;

    // 1. Daftarkan SEMUA kolom baru agar diizinkan masuk ke database (Mass Assignment)
    protected $fillable = [
        'paper_machine_roll_id',
        'tested_by',
        'shift',           // Baru
        'thickness',       // Baru
        'bw',              // Pengganti gsm
        'rct',             // Pengganti ring_crush_test
        'bursting',        // Baru
        'moisture',        // Pengganti moisture_percent
        'cobb_top',        // Baru
        'cobb_bottom',     // Baru
        'plybonding',      // Baru
        'warna',           // Baru
        'status',
        'notes'
    ];

    // 2. Relasi ke tabel Jumbo Roll
    public function paperMachineRoll()
    {
        return $this->belongsTo(PaperMachineRoll::class, 'paper_machine_roll_id');
    }

    // 3. Relasi ke tabel Users (Untuk menampilkan nama Admin Lab yang mengetes)
    public function tester()
    {
        return $this->belongsTo(User::class, 'tested_by');
    }
}