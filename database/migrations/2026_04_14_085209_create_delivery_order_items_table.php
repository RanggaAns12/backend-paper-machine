<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Surat Jalan Induk (Jika DO dihapus, rinciannya ikut terhapus)
            $table->foreignId('delivery_order_id')->constrained('delivery_orders')->onDelete('cascade');
            
            // Relasi ke Fisik Barang. Restrict: Roll yang sudah masuk DO tidak boleh dihapus dari sistem!
            $table->foreignId('finished_good_id')->constrained('finished_goods')->onDelete('restrict'); 
            
            // Berat saat di-scan keluar (menyimpan riwayat berat jika di masa depan ada penyusutan)
            $table->decimal('shipped_weight', 8, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_order_items');
    }
};