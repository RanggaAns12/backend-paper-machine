<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paper_machine_reports', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('users')->cascadeOnDelete();
            
            // ==========================================
            // HEADER SHIFT
            // ==========================================
            $table->string('operator_name'); 
            $table->date('date');
            $table->string('grup');

            // ==========================================
            // PARAMETER KONSUMSI ENERGI (Disesuaikan dengan satuan aslinya)
            // ==========================================
            // Steam hanya Kg/Shift
            $table->decimal('steam_kg', 10, 2)->nullable();
            
            // Water hanya L/Shift
            $table->decimal('water_l', 10, 2)->nullable();
            
            // Power MWh/Shift & Temperature Celcius
            $table->decimal('power_mwh', 10, 4)->nullable(); 
            $table->decimal('temperature_c', 8, 2)->nullable();

            // ==========================================
            // SUMMARY & STATUS
            // ==========================================
            $table->decimal('total_pm', 10, 2)->nullable();
            $table->decimal('total_winder', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            
            // Kolom Status Laporan (Terkunci / Draft)
            $table->boolean('is_locked')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_machine_reports');
    }
};
