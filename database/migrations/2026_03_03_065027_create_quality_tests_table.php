<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_tests', function (Blueprint $table) {
            $table->id();
            
            // 1. Relasi Induk (Jumbo Roll & Penguji)
            $table->foreignId('paper_machine_roll_id')->constrained('paper_machine_rolls')->onDelete('cascade');
            $table->foreignId('tested_by')->constrained('users')->onDelete('cascade'); 
            
            // 2. Informasi Waktu & Shift
            $table->integer('shift')->default(1);
            
            // 3. Quality Monitoring (Parameter Fisik - AVG)
            // Menggunakan decimal(8,3) untuk thickness agar bisa menyimpan angka seperti 0.319
            $table->decimal('thickness', 8, 3)->nullable(); 
            $table->decimal('bw', 8, 2)->nullable();        // Basis Weight / setara GSM
            $table->decimal('rct', 8, 2)->nullable();       // Ring Crush Test
            $table->decimal('bursting', 8, 2)->nullable();  // Bursting Strength
            $table->decimal('moisture', 5, 2)->nullable();  // Kadar Air (%)
            
            // 4. Cobb Size Test
            // Menggunakan integer karena biasanya nilai Cobb itu bulat (contoh: 230, 244)
            $table->integer('cobb_top')->nullable();
            $table->integer('cobb_bottom')->nullable();
            
            // 5. Visual & Perekatan
            $table->enum('plybonding', ['BAIK', 'TIDAK'])->nullable();
            $table->enum('warna', ['SESUAI', 'TIDAK'])->nullable();
            
            // 6. Keputusan Akhir QC
            // Saya ubah menjadi UPPERCASE agar persis dengan nilai form UI kita (PASS/REJECT)
            // Tetap menyisipkan DOWNGRADE untuk jaga-jaga aturan pabrik
            $table->enum('status', ['PASS', 'REJECT', 'DOWNGRADE'])->default('PASS');
            $table->text('notes')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_tests');
    }
};