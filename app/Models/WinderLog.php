<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WinderLog extends Model
{
    use HasFactory;

    // Pastikan nama tabelnya sesuai dengan migration mas
    protected $table = 'winder_logs';

    // Kolom yang boleh diisi
    protected $fillable = [
        'paper_machine_roll_id',
        'operator_id',
        'roll_number',
        'roll_weight',
        'core_diameter',
        'width',
        'status',
        'wound_at'
    ];

    // ✅ RELASI: Winder Log ini dikerjakan oleh Operator siapa?
    public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id');
    }

    // ✅ RELASI: Winder Log ini berasal dari Jumbo Roll mana?
    public function paperMachineRoll()
    {
        // Sesuaikan dengan nama class Model PM Roll mas
        return $this->belongsTo(PaperMachineRoll::class, 'paper_machine_roll_id');
    }
}