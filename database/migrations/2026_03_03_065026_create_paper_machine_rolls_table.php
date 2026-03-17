<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paper_machine_rolls', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel report
            $table->foreignId('report_id')->constrained('paper_machine_reports')->cascadeOnDelete();
            
            $table->integer('no');
            
            // No. Jrk/Intruksi Kerja dan Grade
            $table->string('jrk_instruction')->nullable()->comment('No. Jrk / Instruksi Kerja');
            $table->string('grade')->nullable()->comment('Grade Kertas');

            // No Roll dibuat unik agar tidak boleh ada yang sama
            $table->string('roll_number')->unique();
            
            // Menggunakan float agar .00 hilang pada angka bulat, tapi tetap bisa menampung angka desimal
            $table->float('speed_reel')->nullable();
            $table->float('tonase_roll')->nullable();
            $table->float('width_cm')->nullable();
            $table->float('solid_starch_percent')->nullable();
            
            // Dry strength dalam Kg
            $table->float('dry_strength_kg')->nullable()->comment('Dry Strength dalam satuan Kg');
            
            // Parameter Kimia (Chemicals)
            $table->float('internal_sizing_kg_per_h')->nullable();
            $table->float('floc_l_per_h')->nullable();
            $table->float('coag_l_per_h')->nullable();
            $table->float('yellow_ppm')->nullable();
            $table->float('yellow_l_per_h')->nullable();
            $table->float('red_ppm')->nullable();
            $table->float('red_l_per_h')->nullable();
            $table->float('brown_ppm')->nullable();
            $table->float('brown_l_per_h')->nullable();
            $table->float('external_sizing_kg_per_tp')->nullable();
            $table->float('pac_ml_per_m')->nullable();
            
            // Status untuk tracking apakah draft sudah di-save
            $table->boolean('is_saved')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_machine_rolls');
    }
};