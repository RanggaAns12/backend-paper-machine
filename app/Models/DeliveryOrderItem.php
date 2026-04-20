<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_order_id', 
        'finished_good_id', 
        'shipped_weight'
    ];

    // Relasi balik ke header
    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    // Relasi ke barang fisik di tabel finished_goods
    public function finishedGood()
    {
        return $this->belongsTo(FinishedGood::class);
    }
}