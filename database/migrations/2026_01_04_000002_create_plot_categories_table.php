<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plot sizes offered in a society. The admin enters the size in whichever
 * unit the society advertises (Marla or Kanal); area_marla is the same
 * figure normalised so sizes can be sorted and compared.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plot_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();

            $table->string('block', 60)->nullable();                       // Block A, Phase 2
            $table->enum('plot_type', ['residential', 'commercial', 'farmhouse'])->default('residential');

            $table->decimal('size_value', 8, 2);                           // as entered
            $table->enum('size_unit', ['marla', 'kanal'])->default('marla');
            $table->decimal('area_marla', 10, 2);                          // 1 Kanal = 20 Marla
            $table->string('dimensions', 60)->nullable();                  // 30 x 60 ft

            $table->decimal('price_per_marla', 15, 2)->nullable();
            $table->decimal('total_price', 15, 2);

            $table->enum('availability', ['available', 'limited', 'sold_out'])->default('available');
            $table->unsignedInteger('total_plots')->nullable();
            $table->unsignedInteger('available_plots')->nullable();

            $table->text('notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['society_id', 'sort_order']);
            $table->index('area_marla');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plot_categories');
    }
};
