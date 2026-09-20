<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // author
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title', 180);
            $table->enum('type', [
                'site_visit', 'sales', 'booking', 'expense', 'monthly_performance',
            ]);
            $table->date('report_date')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'report_date']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
