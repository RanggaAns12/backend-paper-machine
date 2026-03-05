<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperMachineRoll extends Model
{
    protected $fillable = [
        'report_id', 'no', 'roll_number', 'speed_reel_wire',
        'tonase_roll', 'width_cm', 'solid_starch_percent',
        'internal_sizing_kg_per_h', 'floc_l_per_h', 'coag_l_per_h',
        'yellow_ppm', 'yellow_l_per_h', 'red_ppm', 'red_l_per_h',
        'brown_ppm', 'brown_l_per_h', 'external_sizing_kg_per_tp', 'pac_ml_per_m',
    ];

    protected $casts = [
        'no'                        => 'integer',
        'speed_reel_wire'           => 'decimal:2',
        'tonase_roll'               => 'decimal:2',
        'width_cm'                  => 'decimal:2',
        'solid_starch_percent'      => 'decimal:2',
        'internal_sizing_kg_per_h'  => 'decimal:2',
        'floc_l_per_h'              => 'decimal:2',
        'coag_l_per_h'              => 'decimal:2',
        'yellow_ppm'                => 'decimal:2',
        'yellow_l_per_h'            => 'decimal:2',
        'red_ppm'                   => 'decimal:2',
        'red_l_per_h'               => 'decimal:2',
        'black_ppm'                 => 'decimal:2',
        'black_l_per_h'             => 'decimal:2',
        'external_sizing_kg_per_tp' => 'decimal:2',
        'pac_ml_per_m'              => 'decimal:2',
    ];

    public function report(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaperMachineReport::class, 'report_id');
    }
}
