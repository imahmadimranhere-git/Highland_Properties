<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Plot sizes are now typed by hand instead of being calculated.
 *
 * The admin writes the size exactly as the society advertises it — "5 Marla",
 * "1 Kanal", "2 Kanal 10 Marla", "1 Acre" — so no conversion rule can ever
 * make the site disagree with the developer's own price list.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plot_categories', function (Blueprint $table) {
            $table->string('size_label', 60)->after('society_id')->default('');
            $table->unsignedInteger('area_sqft')->nullable()->after('size_label');
        });

        // Keep whatever was entered before: "10 Marla", "1 Kanal".
        DB::statement("UPDATE plot_categories
            SET size_label = CONCAT(TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM size_value)), ' ',
                CASE WHEN size_unit = 'kanal' THEN 'Kanal' ELSE 'Marla' END)");

        Schema::table('plot_categories', function (Blueprint $table) {
            $table->dropIndex(['area_marla']);
            $table->dropColumn(['size_value', 'size_unit', 'area_marla']);
        });

        Schema::table('societies', function (Blueprint $table) {
            $table->string('total_area', 80)->nullable()->after('nearby_landmarks');
        });

        DB::statement("UPDATE societies
            SET total_area = CONCAT(TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM total_area_kanal)), ' Kanal')
            WHERE total_area_kanal IS NOT NULL");

        Schema::table('societies', function (Blueprint $table) {
            // The square-feet-per-Marla rule goes with the conversion.
            $table->dropColumn(['total_area_kanal', 'marla_sqft']);
        });
    }

    public function down(): void
    {
        Schema::table('plot_categories', function (Blueprint $table) {
            $table->decimal('size_value', 8, 2)->default(0);
            $table->enum('size_unit', ['marla', 'kanal'])->default('marla');
            $table->decimal('area_marla', 10, 2)->default(0);
            $table->index('area_marla');
            $table->dropColumn(['size_label', 'area_sqft']);
        });

        Schema::table('societies', function (Blueprint $table) {
            $table->decimal('total_area_kanal', 12, 2)->nullable();
            $table->decimal('marla_sqft', 8, 2)->default(225);
            $table->dropColumn('total_area');
        });
    }
};
