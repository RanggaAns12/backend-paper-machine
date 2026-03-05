<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WinderLog extends Model
{
    protected $fillable = [
        'report_id', 'operator_id', 'roll_number',
        'roll_weight', 'core_diameter', 'width', 'status', 'wound_at',
    ];

    protected $casts = [
        'roll_weight'   => 'decimal:2',
        'core_diameter' => 'decimal:2',
        'width'         => 'decimal:2',
        'wound_at'      => 'datetime',
    ];

    public function report(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaperMachineReport::class, 'report_id');
    }

    public function operator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
