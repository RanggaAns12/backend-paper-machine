<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'customer_name',
        'order_date',
        'target_delivery_date',
        'grade',
        'target_qty',
        'notes',
        'status',
    ];

    // ✅ PERBAIKAN RELASI:
    // Argumen ke-2: nama kolom di tabel winder_logs (foreign key)
    // Argumen ke-3: nama kolom di tabel pre_orders (local key)
    public function winderLogs() 
    {
        return $this->hasMany(WinderLog::class, 'po_number', 'po_number');
    }
}