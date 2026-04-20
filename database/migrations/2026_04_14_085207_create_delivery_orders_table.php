<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            
            // Nomor unik Surat Jalan (Contoh: DO-20260417-001)
            $table->string('do_number')->unique(); 
            $table->date('date');
            
            // Identitas Pengiriman
            $table->string('customer_name');              // Tujuan Pabrik Kardus
            $table->string('truck_plate')->nullable();    // Plat nomor armada
            $table->string('driver_name')->nullable();    // Nama supir
            
            // Total akumulasi berat semua roll yang ada di dalam truk (dalam KG)
            $table->decimal('total_tonase', 10, 2)->default(0);
            
            // Siapa Admin Gudang yang membuat dokumen ini
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict'); 
            
            // Draft = Baru dibuat, Ready = Selesai muat barang, Shipped = Truk berangkat
            $table->enum('status', ['draft', 'ready', 'shipped'])->default('draft');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};