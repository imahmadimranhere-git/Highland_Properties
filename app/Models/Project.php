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
        'assigned_consultant_id', 'cover_media_id',
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

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
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
            'city_id', 'location_id', 'cover_media_id', 'sort_order',
        ]);
    }

    public function getFullLocationAttribute(): string
    {
        return collect([$this->location?->name, $this->city?->name])
            ->filter()
            ->implode(', ');
    }
}
