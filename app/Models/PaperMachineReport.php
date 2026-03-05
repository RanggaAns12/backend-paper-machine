<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperMachineReport extends Model
{
    protected $fillable = [
        'machine_id', 'operator_id', 'operator_name', 'date', 'grup', // <-- operator_name ditambahkan
        'working_hour', 'steam', 'water', 'kg_per_shift',
        'l_per_shift', 'power', 'temperature', 'mwh_per_shift',
        'total_pm', 'total_winder', 'remarks',
    ];

    protected $casts = [
        'date'          => 'date',
        // 'working_hour'  => 'decimal:2', // <-- Dihapus karena sekarang string (e.g. "08:00 - 16:00")
        'steam'         => 'decimal:2',
        'water'         => 'decimal:2',
        'kg_per_shift'  => 'decimal:2',
        'l_per_shift'   => 'decimal:2',
        'power'         => 'decimal:2',
        'temperature'   => 'decimal:2',
        'mwh_per_shift' => 'decimal:4',
        'total_pm'      => 'decimal:2',
        'total_winder'  => 'decimal:2',
    ];

    public function machine(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function operator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function rolls(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaperMachineRoll::class, 'report_id');
    }

    public function problems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaperMachineProblem::class, 'report_id');
    }

    public function qualityTests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(QualityTest::class, 'report_id');
    }

    public function winderLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WinderLog::class, 'report_id');
    }
}
