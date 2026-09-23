<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A banner now carries three crops: the existing media_id becomes the laptop
 * image, plus one for tablets and one for phones. A slide only reaches the
 * public site when all three are present.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_sliders', function (Blueprint $table) {
            $table->foreignId('media_id_tablet')->nullable()->after('media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('media_id_mobile')->nullable()->after('media_id_tablet')->constrained('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('home_sliders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('media_id_tablet');
            $table->dropConstrainedForeignId('media_id_mobile');
        });
    }
};
