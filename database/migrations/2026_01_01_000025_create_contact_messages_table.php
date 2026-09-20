<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // General Contact Us form. Kept out of leads so the sales pipeline stays clean.
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('subject', 180)->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['is_read', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
