<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // One plan per unit category (1:1), as specified in the brief.
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_category_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('booking_amount', 15, 2)->default(0);
            $table->decimal('down_payment', 15, 2)->default(0);
            $table->unsignedSmallInteger('installment_count')->default(0);
            $table->enum('installment_frequency', ['monthly', 'quarterly'])->default('monthly');
            $table->decimal('installment_amount', 15, 2)->default(0);
            $table->decimal('possession_charges', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};
