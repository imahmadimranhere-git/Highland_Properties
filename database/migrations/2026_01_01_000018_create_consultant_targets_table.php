<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Feeds the "target vs achievement" widget on the consultant dashboard.
    public function up(): void
    {
        Schema::create('consultant_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('period', 7);            // YYYY-MM
            $table->unsignedInteger('target_deals')->default(0);
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultant_targets');
    }
};
