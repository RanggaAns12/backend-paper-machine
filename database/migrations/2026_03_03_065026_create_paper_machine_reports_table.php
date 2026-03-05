<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paper_machine_reports', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel machines
            $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
            
            // operator_id = Akun user (admin-pm) yang sedang login di komputer
            $table->foreignId('operator_id')->constrained('users')->cascadeOnDelete();
            
            // ==========================================
            // TAMBAHAN BARU: Nama Fisik Kepala Shift
            // ==========================================
            $table->string('operator_name'); 

            $table->date('date');
            $table->string('grup');
            
            // DIUBAH: Menjadi string agar bisa simpan "08:00 - 16:00"
            $table->string('working_hour'); 

            // DIUBAH: Disamakan dengan Form UI agar gampang di-mapping
            $table->decimal('steam_kg_shift', 10, 2)->nullable();
            $table->decimal('steam_l_shift', 10, 2)->nullable();
            $table->decimal('water_kg_shift', 10, 2)->nullable();
            $table->decimal('water_l_shift', 10, 2)->nullable();
            $table->decimal('power_mwh', 10, 4)->nullable(); 
            $table->decimal('temperature', 8, 2)->nullable();

            // Kalkulasi Total
            $table->decimal('total_pm', 10, 2)->nullable();
            $table->decimal('total_winder', 10, 2)->nullable();
            
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_machine_reports');
    }
};
