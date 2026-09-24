<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Google's own embed links run to 600–900 characters, so the old 500-character
 * column silently refused them and the page fell back to the address.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->text('map_embed_url')->nullable()->change());
        Schema::table('societies', fn (Blueprint $table) => $table->text('map_embed_url')->nullable()->change());
    }

    public function down(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->string('map_embed_url', 500)->nullable()->change());
        Schema::table('societies', fn (Blueprint $table) => $table->string('map_embed_url', 500)->nullable()->change());
    }
};
