<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperMachineReport extends Model
{
    protected $fillable = [
        'machine_id', 
        'operator_id', 
        'operator_name', 
        'date', 
        'grup', 
        'steam_kg',       // Sesuai migration
        'water_l',        // Sesuai migration
        'power_mwh',      // Sesuai migration
        'temperature_c',  // Sesuai migration
        'total_pm', 
        'total_winder', 
        'remarks',
        'is_locked'       // Tambahan dari migration
    ];

    protected $casts = [
        'date'          => 'date',
        'steam_kg'      => 'decimal:2',
        'water_l'       => 'decimal:2',
        'power_mwh'     => 'decimal:4',
        'temperature_c' => 'decimal:2',
        'total_pm'      => 'decimal:2',
        'total_winder'  => 'decimal:2',
        'is_locked'     => 'boolean',
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
