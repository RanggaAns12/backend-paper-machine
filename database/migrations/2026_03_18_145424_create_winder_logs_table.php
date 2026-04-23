<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration untuk membuat tabel winder_logs.
     */
    public function up(): void
    {
        Schema::create('winder_logs', function (Blueprint $table) {
            $table->id();
            
            // ✅ INTEGRASI PRE-ORDER (GUDANG)
            // Opsional (nullable) karena Winder bisa memproduksi Free Stock (tanpa PO)
            $table->string('po_number')->nullable();
            
            // 1. Relasi ke Jumbo Roll (Paper Machine Roll) yang sedang dipotong
            // Perbaikan: Diarahkan ke paper_machine_rolls agar traceability terjamin
            $table->foreignId('paper_machine_roll_id')
                  ->constrained('paper_machine_rolls')
                  ->cascadeOnDelete();
            
            // 2. Relasi ke Tabel Operator (Karyawan lapangan, BUKAN users)
            $table->foreignId('operator_id')
                  ->constrained('operators')
                  ->cascadeOnDelete();
            
            // 3. Detail Spesifikasi Potongan Roll Winder
            // Perbaikan: Ditambahkan ->unique() untuk mencegah duplikasi barcode
            $table->string('roll_number')->unique(); 
            $table->decimal('roll_weight', 10, 2)->nullable(); // Berat roll (Kg)
            $table->decimal('core_diameter', 8, 2)->nullable(); // Diameter inti
            $table->decimal('width', 10, 2)->nullable(); // Lebar potongan
            
            // 4. Status Pengerjaan Winder
            $table->enum('status', ['pending', 'done'])->default('pending');
            
            // 5. Waktu aktual saat roll selesai digulung
            $table->timestamp('wound_at')->nullable();
            
            // 6. Pencatatan waktu standar Laravel
            $table->timestamps();
        });
    }

    /**
     * Membatalkan/menghapus migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('winder_logs');
    }
};