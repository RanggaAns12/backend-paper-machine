<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paper_machine_rolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('paper_machine_reports')->onDelete('cascade');
            
            $table->integer('no');
            $table->string('working_hour')->nullable();
            $table->string('jrk_instruction')->nullable();
            $table->string('grade')->nullable();
            $table->string('roll_number')->unique();
            
            // Parameter Produksi
            $table->decimal('speed_reel', 8, 2)->nullable();
            $table->decimal('tonase_roll', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            
            // Parameter Kimia
            $table->decimal('solid_starch_percent', 8, 2)->nullable();
            $table->decimal('dry_strength_kg', 8, 2)->nullable();
            $table->decimal('floc_l_per_h', 8, 2)->nullable();
            $table->decimal('coag_l_per_h', 8, 2)->nullable();
            $table->decimal('brown_ppm', 8, 2)->nullable();
            $table->decimal('brown_l_per_h', 8, 2)->nullable();
            $table->decimal('yellow_ppm', 8, 2)->nullable();
            $table->decimal('yellow_l_per_h', 8, 2)->nullable();
            $table->decimal('red_ppm', 8, 2)->nullable();
            $table->decimal('red_l_per_h', 8, 2)->nullable();
            $table->decimal('external_sizing_kg_per_tp', 8, 2)->nullable();
            $table->decimal('pac_ml_per_m', 8, 2)->nullable();
            
            $table->boolean('is_saved')->default(true);
            
            // ✅ INI KOLOM BARU UNTUK ALUR QC LAB
            $table->enum('qc_status', ['pending', 'passed', 'reject', 'downgrade'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_machine_rolls');
    }
};