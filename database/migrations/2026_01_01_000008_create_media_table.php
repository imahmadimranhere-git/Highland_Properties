<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Central media library. Every upload is converted once at upload time,
    // so listing pages can serve thumb_path and detail pages webp_path.
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('disk', 30)->default('public');
            $table->string('path');
            $table->string('webp_path')->nullable();
            $table->string('thumb_path')->nullable();
            $table->string('original_name');
            $table->string('mime', 100);
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('folder', 60)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('folder');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
