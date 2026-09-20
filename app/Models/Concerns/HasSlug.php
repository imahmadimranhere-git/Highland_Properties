<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Generates a unique slug on create. Slugs are NOT regenerated on update,
 * so published URLs never break by accident. Admins can still edit the slug by hand.
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $source = filled($model->slug)
                ? $model->slug
                : (string) $model->{$model->slugSourceColumn()};

            $model->slug = $model->generateUniqueSlug($source);
        });
    }

    public function slugSourceColumn(): string
    {
        return 'name';
    }

    public function generateUniqueSlug(string $source): string
    {
        $base = Str::slug($source) ?: Str::random(8);
        $slug = $base;
        $suffix = 2;

        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive(static::class), true);

        while (true) {
            $query = static::query()->where('slug', $slug);

            // A soft-deleted record still owns its slug, so include trashed rows.
            if ($usesSoftDeletes) {
                $query->withTrashed();
            }

            if ($this->exists) {
                $query->whereKeyNot($this->getKey());
            }

            // Models whose slug is unique per parent narrow the check here.
            $this->applySlugScope($query);

            if (! $query->exists()) {
                return $slug;
            }

            $slug = $base . '-' . $suffix++;
        }
    }

    /** Hook for models with scoped slug uniqueness (see Location). */
    protected function applySlugScope(Builder $query): void
    {
        //
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
