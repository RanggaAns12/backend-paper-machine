<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paper_machine_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('paper_machine_reports')->cascadeOnDelete();
            $table->integer('no');
            $table->text('description');
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_machine_problems');
    }
};
