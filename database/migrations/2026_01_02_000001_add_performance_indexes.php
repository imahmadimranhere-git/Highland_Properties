<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes found missing in the step 7 query audit. Each one matches a
 * WHERE / ORDER BY that the application actually runs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Dashboard monthly chart and "leads this month" filter on
            // created_at alone; (status, created_at) cannot serve that.
            $table->index('created_at');
            // Sales reports: won deals grouped by the month they closed.
            $table->index(['status', 'closed_at']);
        });

        Schema::table('reports', function (Blueprint $table) {
            // Admin report list filtered by date across all consultants.
            $table->index('report_date');
        });

        Schema::table('projects', function (Blueprint $table) {
            // Public grid: WHERE is_published = 1 ORDER BY sort_order.
            $table->index(['is_published', 'sort_order']);
        });

        Schema::table('media', function (Blueprint $table) {
            // Media library is listed newest first.
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'closed_at']);
        });

        Schema::table('reports', fn (Blueprint $table) => $table->dropIndex(['report_date']));
        Schema::table('projects', fn (Blueprint $table) => $table->dropIndex(['is_published', 'sort_order']));
        Schema::table('media', fn (Blueprint $table) => $table->dropIndex(['created_at']));
    }
};
