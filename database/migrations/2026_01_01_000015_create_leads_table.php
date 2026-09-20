<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name', 150);
            $table->string('phone', 30);
            $table->string('email', 150)->nullable();
            $table->text('message')->nullable();

            $table->enum('status', [
                'new', 'contacted', 'site_visit', 'negotiation', 'closed_won', 'closed_lost',
            ])->default('new');
            $table->enum('source', ['website', 'manual', 'call', 'whatsapp'])->default('website');

            $table->date('next_follow_up_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('deal_value', 15, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Consultant pipeline view.
            $table->index(['assigned_to', 'status']);
            // Admin lead list + monthly chart.
            $table->index(['status', 'created_at']);
            // Today's reminders widget.
            $table->index('next_follow_up_at');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
