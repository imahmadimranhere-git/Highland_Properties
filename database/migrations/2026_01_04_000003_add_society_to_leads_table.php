<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** An inquiry can now come from a society page as well as a project page. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('society_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
            $table->foreignId('plot_category_id')->nullable()->after('unit_category_id')->constrained()->nullOnDelete();
            $table->index('society_id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['society_id']);
            $table->dropConstrainedForeignId('society_id');
            $table->dropConstrainedForeignId('plot_category_id');
        });
    }
};
