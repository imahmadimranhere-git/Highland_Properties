<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('slug', 191);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Same society name may exist in two cities, so uniqueness is per city.
            $table->unique(['city_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
