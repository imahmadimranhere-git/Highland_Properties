<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A small picture of where the property is — a map screenshot or a photo of
 * the area — shown on the listing card in place of the location text.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->foreignId('location_media_id')->nullable()->after('cover_media_id_mobile')->constrained('media')->nullOnDelete();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('location_media_id')->nullable()->after('cover_media_id_mobile')->constrained('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('societies', fn (Blueprint $table) => $table->dropConstrainedForeignId('location_media_id'));
        Schema::table('projects', fn (Blueprint $table) => $table->dropConstrainedForeignId('location_media_id'));
    }
};
