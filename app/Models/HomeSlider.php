<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeSlider extends Model
{
    protected $fillable = [
        'media_id', 'title', 'subtitle', 'cta_label', 'cta_url', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
