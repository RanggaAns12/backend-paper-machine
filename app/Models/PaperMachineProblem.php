<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperMachineProblem extends Model
{
    protected $fillable = [
        'report_id', 'no', 'description',
        'time_start', 'time_end', 'duration_minutes',
    ];

    protected $casts = [
        'no'               => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function report(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaperMachineReport::class, 'report_id');
    }
}
