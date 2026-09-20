<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'user_id', 'name', 'designation', 'photo', 'bio', 'phone', 'email',
        'whatsapp', 'linkedin', 'facebook', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
