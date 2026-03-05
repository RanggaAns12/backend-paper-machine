<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = ['name', 'type', 'status', 'description'];

    public function paperMachineReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaperMachineReport::class);
    }
}
