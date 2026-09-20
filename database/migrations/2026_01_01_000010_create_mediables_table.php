<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Polymorphic pivot: one media row can be attached to a project gallery,
    // a development update, a blog post or the home slider.
    public function up(): void
    {
        Schema::create('mediables', function (Blueprint $table) {
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->morphs('mediable');
            // gallery | floor_plan | update_photo | slider | cover
            $table->string('collection', 30)->default('gallery');
            $table->unsignedInteger('sort_order')->default(0);

            $table->unique(
                ['media_id', 'mediable_id', 'mediable_type', 'collection'],
                'mediables_unique'
            );
            $table->index(
                ['mediable_type', 'mediable_id', 'collection'],
                'mediables_morph_collection_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mediables');
    }
};
