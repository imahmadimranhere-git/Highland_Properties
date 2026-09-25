<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One optional external link per post: the words to show, and where they go.
 * Both columns are nullable, so every existing post keeps working untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('external_link_text', 160)->nullable()->after('content');
            $table->string('external_link_url', 500)->nullable()->after('external_link_text');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['external_link_text', 'external_link_url']);
        });
    }
};
