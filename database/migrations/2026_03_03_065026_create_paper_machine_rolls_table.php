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
            
            // Kecepatan Mesin (Hanya menyisakan Reel)
            $table->decimal('speed_reel', 10, 2)->nullable();
            
            // Parameter Kertas & Produksi
            $table->decimal('tonase_roll', 10, 2)->nullable();
            $table->decimal('width_cm', 10, 2)->nullable();
            $table->decimal('solid_starch_percent', 8, 2)->nullable();
            
            // Dry strength dalam Kg
            $table->decimal('dry_strength_kg', 10, 2)->nullable()->comment('Dry Strength dalam satuan Kg');
            
            // Parameter Kimia (Chemicals)
            $table->decimal('internal_sizing_kg_per_h', 10, 2)->nullable();
            $table->decimal('floc_l_per_h', 10, 2)->nullable();
            $table->decimal('coag_l_per_h', 10, 2)->nullable();
            $table->decimal('yellow_ppm', 10, 2)->nullable();
            $table->decimal('yellow_l_per_h', 10, 2)->nullable();
            $table->decimal('red_ppm', 10, 2)->nullable();
            $table->decimal('red_l_per_h', 10, 2)->nullable();
            $table->decimal('brown_ppm', 10, 2)->nullable();
            $table->decimal('brown_l_per_h', 10, 2)->nullable();
            $table->decimal('external_sizing_kg_per_tp', 10, 2)->nullable();
            $table->decimal('pac_ml_per_m', 10, 2)->nullable();
            
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