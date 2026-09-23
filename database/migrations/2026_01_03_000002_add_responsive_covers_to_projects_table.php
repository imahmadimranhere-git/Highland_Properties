<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A project cover now carries three crops. The existing cover_media_id stays
 * the laptop image, so nothing already uploaded is lost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('cover_media_id_tablet')->nullable()->after('cover_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('cover_media_id_mobile')->nullable()->after('cover_media_id_tablet')->constrained('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cover_media_id_tablet');
            $table->dropConstrainedForeignId('cover_media_id_mobile');
        });
    }
};
