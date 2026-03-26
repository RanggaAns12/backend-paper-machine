<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperMachineRoll extends Model
{
    protected $fillable = [
        'report_id',
        'no',
        'working_hour', // ✅ Ditambahkan
        'jrk_instruction',
        'grade',
        'roll_number',
        'speed_reel',
        'tonase_roll',
        'width_cm',
        'solid_starch_percent',
        'dry_strength_kg',
        // internal_sizing_kg_per_h sudah dihapus
        'floc_l_per_h',
        'coag_l_per_h',
        'brown_ppm',       // ✅ Diurutkan: Brown, Yellow, Red
        'brown_l_per_h',
        'yellow_ppm',
        'yellow_l_per_h',
        'red_ppm',
        'red_l_per_h',
        'external_sizing_kg_per_tp',
        'pac_ml_per_m',
        'is_saved',
    ];

    protected $casts = [
        'report_id'                 => 'integer',
        'no'                        => 'integer',
        'working_hour'              => 'string', // ✅ Ditambahkan
        'speed_reel'                => 'decimal:2',
        'tonase_roll'               => 'decimal:2',
        'width_cm'                  => 'decimal:2',
        'solid_starch_percent'      => 'decimal:2',
        'dry_strength_kg'           => 'decimal:2',
        // internal_sizing_kg_per_h sudah dihapus
        'floc_l_per_h'              => 'decimal:2',
        'coag_l_per_h'              => 'decimal:2',
        'brown_ppm'                 => 'decimal:2', // ✅ Diurutkan
        'brown_l_per_h'             => 'decimal:2',
        'yellow_ppm'                => 'decimal:2',
        'yellow_l_per_h'            => 'decimal:2',
        'red_ppm'                   => 'decimal:2',
        'red_l_per_h'               => 'decimal:2',
        'external_sizing_kg_per_tp' => 'decimal:2',
        'pac_ml_per_m'              => 'decimal:2',
        'is_saved'                  => 'boolean',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(PaperMachineReport::class, 'report_id');
    }
}