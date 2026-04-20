<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'do_number', 
        'date', 
        'customer_name', 
        'truck_plate', 
        'driver_name', 
        'total_tonase', 
        'created_by', 
        'status'
    ];

    // Relasi ke rincian muatan (Satu surat jalan punya banyak item)
    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }

    // Relasi ke pembuat dokumen
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}