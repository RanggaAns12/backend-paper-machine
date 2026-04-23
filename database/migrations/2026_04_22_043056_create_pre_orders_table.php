<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->string('customer_name');
            $table->date('order_date');
            $table->date('target_delivery_date');
            $table->string('grade');
            $table->decimal('target_qty', 10, 2); // Target dalam Kilogram
            $table->text('notes')->nullable();
            $table->enum('status', ['PENDING', 'ON_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_orders');
    }
};