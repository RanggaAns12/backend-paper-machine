<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'username',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password'  => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Wajib ditambahkan agar Spatie tahu guard User = 'web'
     * sehingga sinkron dengan role yang dibuat di guard 'web'
     */
    public function guardName(): string
    {
        return 'web';
    }

    public function paperMachineReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaperMachineReport::class, 'operator_id');
    }

    public function qualityTests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(QualityTest::class, 'tested_by');
    }

    public function winderLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WinderLog::class, 'operator_id');
    }
}
