<?php

namespace App\Models;

use App\Enums\TestimonialStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    protected $fillable = [
        'project_id', 'name', 'designation', 'rating', 'message',
        'youtube_url', 'photo', 'status', 'sort_order',
    ];

    protected $casts = [
        'status' => TestimonialStatus::class,
        'rating' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** True when a usable YouTube link is saved. */
    public function hasVideo(): bool
    {
        return \App\Support\Youtube::id($this->youtube_url) !== null;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', TestimonialStatus::Approved->value)->orderBy('sort_order');
    }
}
