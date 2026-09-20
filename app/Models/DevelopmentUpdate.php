<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevelopmentUpdate extends Model
{
    use HasMedia;
    use SoftDeletes;

    protected $fillable = ['project_id', 'title', 'update_date', 'description', 'is_published'];

    protected $casts = [
        'update_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
