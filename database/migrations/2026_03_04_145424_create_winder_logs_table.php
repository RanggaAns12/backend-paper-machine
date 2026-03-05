<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('winder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('paper_machine_reports')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('users')->cascadeOnDelete();
            $table->string('roll_number');
            $table->decimal('roll_weight', 10, 2)->nullable();
            $table->decimal('core_diameter', 8, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->timestamp('wound_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('winder_logs');
    }
};
    