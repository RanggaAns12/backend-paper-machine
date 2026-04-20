<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WinderLog extends Model
{
    use HasFactory;

    protected $table = 'winder_logs';

    protected $fillable = [
        'paper_machine_roll_id',
        'operator_id',
        'roll_number', // Ini yang akan menjadi Barcode
        'roll_weight',
        'core_diameter',
        'width',
        'status',
        'wound_at'
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'operator_id');
    }

    public function paperMachineRoll(): BelongsTo
    {
        return $this->belongsTo(PaperMachineRoll::class, 'paper_machine_roll_id');
    }

    public function finishedGood(): HasOne
    {
        return $this->hasOne(FinishedGood::class, 'winder_log_id');
    }
}