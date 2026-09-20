<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cover_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('title', 200);
            $table->string('slug', 191)->unique();
            $table->string('excerpt', 320)->nullable();
            $table->longText('content')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
