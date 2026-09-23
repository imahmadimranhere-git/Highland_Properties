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

class Project extends Model
{
    use HasMedia;
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'developer_id', 'city_id', 'location_id', 'project_type_id',
        'assigned_consultant_id', 'cover_media_id', 'cover_media_id_tablet', 'cover_media_id_mobile',
        'name', 'slug', 'ownership_flag', 'status', 'short_description', 'description',
        'address', 'latitude', 'longitude', 'map_embed_url', 'nearby_landmarks',
        'total_area', 'total_floors', 'total_units', 'completion_target', 'approvals',
        'starting_price', 'brochure_path',
        'is_featured', 'is_published', 'sort_order', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'ownership_flag' => OwnershipFlag::class,
        'nearby_landmarks' => 'array',
        'completion_target' => 'date',
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

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_consultant_id');
    }

    /** Laptop / desktop cover. */
    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function coverTablet(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id_tablet');
    }

    public function coverMobile(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id_mobile');
    }

    /**
     * The cover crop for one screen size, falling back to the laptop image.
     *
     * Never triggers a query: if the relation was not eager-loaded (an older
     * cached collection, for example) it simply falls back instead of
     * throwing a lazy-loading violation.
     */
    public function coverFor(string $size): ?Media
    {
        $relation = match ($size) {
            'tablet' => 'coverTablet',
            'mobile' => 'coverMobile',
            default => 'cover',
        };

        $crop = $this->relationLoaded($relation) ? $this->getRelation($relation) : null;

        // A crop that is loaded but empty still falls back to the laptop image,
        // so older projects keep working until all three sizes are uploaded.
        return $crop ?? ($this->relationLoaded('cover') ? $this->getRelation('cover') : null);
    }

    /** All three crops uploaded. */
    public function hasAllCovers(): bool
    {
        return $this->cover_media_id && $this->cover_media_id_tablet && $this->cover_media_id_mobile;
    }

    /** Which crops are still missing, for the admin warning. */
    public function missingCoverSizes(): array
    {
        return array_keys(array_filter([
            'laptop' => ! $this->cover_media_id,
            'tablet' => ! $this->cover_media_id_tablet,
            'mobile' => ! $this->cover_media_id_mobile,
        ]));
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function unitCategories(): HasMany
    {
        return $this->hasMany(UnitCategory::class)->orderBy('sort_order');
    }

    public function developmentUpdates(): HasMany
    {
        return $this->hasMany(DevelopmentUpdate::class)->orderByDesc('update_date');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Columns needed by a project card. Selecting only these keeps the
     * listing query light — description and map columns are never loaded there.
     */
    public function scopeForCard(Builder $query): Builder
    {
        return $query->select([
            'id', 'name', 'slug', 'status', 'starting_price', 'short_description',
            'city_id', 'location_id', 'sort_order',
            'cover_media_id', 'cover_media_id_tablet', 'cover_media_id_mobile',
        ]);
    }

    public function getFullLocationAttribute(): string
    {
        return collect([$this->location?->name, $this->city?->name])
            ->filter()
            ->implode(', ');
    }
}
