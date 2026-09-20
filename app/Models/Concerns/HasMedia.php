<?php

namespace App\Models\Concerns;

use App\Enums\MediaCollection;
use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Attaches media library rows to any model through the `mediables` pivot.
 * Always eager-load the collection you need, e.g. with('gallery'),
 * otherwise a listing page will fire one query per row.
 */
trait HasMedia
{
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')
            ->withPivot(['collection', 'sort_order'])
            ->orderBy('mediables.sort_order');
    }

    public function gallery(): MorphToMany
    {
        return $this->media()->wherePivot('collection', MediaCollection::Gallery->value);
    }

    public function floorPlans(): MorphToMany
    {
        return $this->media()->wherePivot('collection', MediaCollection::FloorPlan->value);
    }

    public function photos(): MorphToMany
    {
        return $this->media()->wherePivot('collection', MediaCollection::UpdatePhoto->value);
    }

    /**
     * Replace one collection with the given ordered media ids.
     *
     * @param  array<int, int>  $mediaIds
     */
    public function syncMediaCollection(MediaCollection $collection, array $mediaIds): void
    {
        $this->media()->wherePivot('collection', $collection->value)->detach();

        foreach (array_values($mediaIds) as $index => $mediaId) {
            $this->media()->attach($mediaId, [
                'collection' => $collection->value,
                'sort_order' => $index,
            ]);
        }
    }
}
