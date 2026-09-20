<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);              // Category A, Category B ...
            $table->string('unit_type', 120);        // 2 Bed Apartment, Shop, Villa ...
            $table->decimal('size_value', 10, 2)->nullable();
            $table->string('size_unit', 20)->default('sq ft');
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->decimal('total_price', 15, 2);
            $table->enum('availability', ['available', 'limited', 'sold_out'])->default('available');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_categories');
    }
};
