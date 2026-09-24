<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Housing societies: land sold as plots, measured in Marla and Kanal.
 * Kept separate from projects, which are buildings sold as units.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('societies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_consultant_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('cover_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('cover_media_id_tablet')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('cover_media_id_mobile')->nullable()->constrained('media')->nullOnDelete();

            $table->string('name', 180);
            $table->string('slug', 191)->unique();
            $table->enum('ownership_flag', ['marketed', 'own_development'])->default('marketed');
            $table->enum('status', ['ongoing', 'completed', 'upcoming', 'for_sale', 'for_rent', 'sold_out'])->default('ongoing');

            $table->string('short_description', 320)->nullable();
            $table->longText('description')->nullable();

            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('map_embed_url', 500)->nullable();
            $table->json('nearby_landmarks')->nullable();

            /* Society facts ------------------------------------------------ */
            $table->decimal('total_area_kanal', 12, 2)->nullable();   // whole scheme size
            $table->unsignedInteger('total_plots')->nullable();
            $table->string('noc_status', 160)->nullable();            // e.g. "RDA approved, NOC #123"
            $table->string('development_charges', 160)->nullable();
            $table->date('possession_target')->nullable();

            /*
             * Square feet in one Marla differs by area: 225 in most new
             * schemes, 272.25 where the old measure is still used. The admin
             * sets it per society so the sq ft column is never wrong.
             */
            $table->decimal('marla_sqft', 8, 2)->default(225);

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

            $table->index(['is_published', 'sort_order']);
            $table->index(['is_featured', 'is_published']);
        });

        Schema::create('amenity_society', function (Blueprint $table) {
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->primary(['society_id', 'amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_society');
        Schema::dropIfExists('societies');
    }
};
