<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->date('update_date');
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Public timeline reads newest first for a single project.
            $table->index(['project_id', 'is_published', 'update_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_updates');
    }
};
