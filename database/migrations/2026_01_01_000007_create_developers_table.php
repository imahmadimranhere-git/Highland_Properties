<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160);
            $table->string('slug', 191)->unique();
            $table->string('logo_path')->nullable();
            $table->text('background')->nullable();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->unsignedSmallInteger('completed_projects')->default(0);
            $table->string('contact_person', 120)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developers');
    }
};
