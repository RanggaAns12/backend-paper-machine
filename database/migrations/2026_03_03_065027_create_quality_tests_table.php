<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quality_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('paper_machine_reports')->cascadeOnDelete();
            $table->foreignId('tested_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('moisture', 8, 2)->nullable();
            $table->decimal('tensile_strength', 10, 2)->nullable();
            $table->decimal('brightness', 8, 2)->nullable();
            $table->decimal('smoothness', 8, 2)->nullable();
            $table->enum('result', ['pass', 'fail']);
            $table->text('notes')->nullable();
            $table->timestamp('tested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_tests');
    }
};
