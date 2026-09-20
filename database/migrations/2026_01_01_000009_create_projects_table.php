<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_type_id')->nullable()->constrained()->nullOnDelete();
            // Consultant who owns inquiries for this project by default.
            $table->foreignId('assigned_consultant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cover_media_id')->nullable()->constrained('media')->nullOnDelete();

            $table->string('name', 180);
            $table->string('slug', 191)->unique();
            $table->enum('ownership_flag', ['marketed', 'own_development'])->default('marketed');
            $table->enum('status', [
                'ongoing', 'completed', 'upcoming', 'for_sale', 'for_rent', 'sold_out',
            ])->default('ongoing');

            $table->string('short_description', 320)->nullable();
            $table->longText('description')->nullable();

            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // Stored, never rendered directly: the map iframe is built lazily on click.
            $table->string('map_embed_url', 500)->nullable();
            $table->json('nearby_landmarks')->nullable();

            $table->string('total_area', 60)->nullable();
            $table->unsignedSmallInteger('total_floors')->nullable();
            $table->unsignedInteger('total_units')->nullable();
            $table->date('completion_target')->nullable();
            $table->string('approvals')->nullable();
            $table->decimal('starting_price', 15, 2)->nullable();
            $table->string('brochure_path')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Covers the public listing query (published + status) and the home page
            // featured query without a full table scan.
            $table->index(['is_published', 'status']);
            $table->index(['is_featured', 'is_published']);
            $table->index('assigned_consultant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
