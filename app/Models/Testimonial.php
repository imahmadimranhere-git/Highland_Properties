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
        'photo', 'status', 'sort_order',
    ];

    protected $casts = [
        'status' => TestimonialStatus::class,
        'rating' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', TestimonialStatus::Approved->value)->orderBy('sort_order');
    }
}
