<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finished_goods', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('winder_log_id')->constrained('winder_logs')->onDelete('cascade');
            
            $table->string('roll_number')->unique();
            $table->decimal('roll_weight', 8, 2);
            $table->decimal('width', 8, 2)->nullable();
            $table->integer('core_diameter')->nullable();
            
            // 🔥 PERBAIKAN: Disesuaikan untuk penyimpanan lantai (Floor Storage)
            $table->string('location_block')->nullable(); // Contoh: Blok A, Blok B
            $table->string('location_line')->nullable();  // Contoh: Jalur 01, Jalur 02
            
            $table->enum('status', ['in_stock', 'shipped', 'quarantined'])->default('in_stock');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finished_goods');
    }
};