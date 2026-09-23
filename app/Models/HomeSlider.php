<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeSlider extends Model
{
    protected $fillable = [
        'media_id', 'media_id_tablet', 'media_id_mobile',
        'title', 'subtitle', 'cta_label', 'cta_url', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    /** Laptop / desktop image. */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function tablet(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id_tablet');
    }

    public function mobile(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id_mobile');
    }

    /** All three crops uploaded. */
    public function isComplete(): bool
    {
        return $this->media_id && $this->media_id_tablet && $this->media_id_mobile;
    }

    /** Which crops are still missing, for the admin warning. */
    public function missingSizes(): array
    {
        return array_keys(array_filter([
            'laptop' => ! $this->media_id,
            'tablet' => ! $this->media_id_tablet,
            'mobile' => ! $this->media_id_mobile,
        ]));
    }

    /**
     * Only complete, switched-on slides reach the website. A half-finished
     * banner can be saved and worked on without ever showing to visitors.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('media_id')
            ->whereNotNull('media_id_tablet')
            ->whereNotNull('media_id_mobile')
            ->orderBy('sort_order');
    }
}
