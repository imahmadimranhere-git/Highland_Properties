<?php

namespace App\Models;

use App\Enums\OwnershipFlag;
use App\Enums\ProjectStatus;
use App\Models\Concerns\HasMedia;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Society extends Model
{
    use HasMedia;
    use HasSlug;
    use SoftDeletes;

    protected $table = 'societies';

    protected $fillable = [
        'developer_id', 'city_id', 'location_id', 'assigned_consultant_id',
        'cover_media_id', 'cover_media_id_tablet', 'cover_media_id_mobile',
        'location_media_id',
        'name', 'slug', 'ownership_flag', 'status', 'short_description', 'description',
        'address', 'latitude', 'longitude', 'map_embed_url', 'nearby_landmarks',
        'total_area', 'total_plots', 'noc_status', 'development_charges',
        'possession_target', 'starting_price', 'brochure_path',
        'is_featured', 'is_published', 'sort_order', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'ownership_flag' => OwnershipFlag::class,
        'nearby_landmarks' => 'array',
        'possession_target' => 'date',
        'starting_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_consultant_id');
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    /** Map screenshot or area photo shown on the listing card. */
    public function locationImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'location_media_id');
    }

    public function coverTablet(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id_tablet');
    }

    public function coverMobile(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id_mobile');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function plotCategories(): HasMany
    {
        return $this->hasMany(PlotCategory::class)->orderBy('sort_order')->orderBy('id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /** Columns a society card needs — nothing heavier is loaded for a grid. */
    public function scopeForCard(Builder $query): Builder
    {
        return $query->select([
            'id', 'name', 'slug', 'status', 'starting_price', 'short_description',
            'city_id', 'location_id', 'sort_order', 'total_plots',
            'cover_media_id', 'cover_media_id_tablet', 'cover_media_id_mobile',
        'location_media_id',
        ]);
    }

    /** Same safe fallback as projects: never triggers a lazy load. */
    public function coverFor(string $size): ?Media
    {
        $relation = match ($size) {
            'tablet' => 'coverTablet',
            'mobile' => 'coverMobile',
            default => 'cover',
        };

        $crop = $this->relationLoaded($relation) ? $this->getRelation($relation) : null;

        return $crop ?? ($this->relationLoaded('cover') ? $this->getRelation('cover') : null);
    }

    public function hasAllCovers(): bool
    {
        return $this->cover_media_id && $this->cover_media_id_tablet && $this->cover_media_id_mobile;
    }

    public function missingCoverSizes(): array
    {
        return array_keys(array_filter([
            'laptop' => ! $this->cover_media_id,
            'tablet' => ! $this->cover_media_id_tablet,
            'mobile' => ! $this->cover_media_id_mobile,
        ]));
    }

    public function getFullLocationAttribute(): string
    {
        return collect([$this->location?->name, $this->city?->name])->filter()->implode(', ');
    }
}
