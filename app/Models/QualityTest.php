<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityTest extends Model
{
    protected $fillable = [
        'report_id', 'tested_by', 'moisture', 'tensile_strength',
        'brightness', 'smoothness', 'result', 'notes', 'tested_at',
    ];

    protected $casts = [
        'moisture'         => 'decimal:2',
        'tensile_strength' => 'decimal:2',
        'brightness'       => 'decimal:2',
        'smoothness'       => 'decimal:2',
        'tested_at'        => 'datetime',
    ];

    public function report(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaperMachineReport::class, 'report_id');
    }

    public function tester(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'tested_by');
    }
}
