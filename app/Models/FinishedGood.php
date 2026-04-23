<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinishedGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'winder_log_id', 
        'roll_number', 
        'roll_weight', 
        'width', 
        'core_diameter',
        'grade', // ✅ Tambahkan ini
        'status'
    ];

    public function winderLog(): BelongsTo
    {
        return $this->belongsTo(WinderLog::class, 'winder_log_id');
    }
}