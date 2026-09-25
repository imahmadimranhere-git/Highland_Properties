<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'author_id', 'cover_media_id', 'title', 'slug', 'excerpt', 'content',
        'external_link_text', 'external_link_url',
        'published_at', 'is_published', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function slugSourceColumn(): string
    {
        return 'title';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    /**
     * True only when both halves of the link exist. The blade asks this one
     * question instead of checking two columns in two places.
     */
    public function hasExternalLink(): bool
    {
        return filled($this->external_link_text) && filled($this->external_link_url);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
