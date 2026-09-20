<?php

namespace App\Models;

use App\Enums\UnitAvailability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnitCategory extends Model
{
    protected $fillable = [
        'project_id', 'name', 'unit_type', 'size_value', 'size_unit',
        'bedrooms', 'bathrooms', 'total_price', 'availability', 'sort_order',
    ];

    protected $casts = [
        'availability' => UnitAvailability::class,
        'size_value' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function paymentPlan(): HasOne
    {
        return $this->hasOne(PaymentPlan::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function getSizeLabelAttribute(): ?string
    {
        return $this->size_value ? rtrim(rtrim((string) $this->size_value, '0'), '.') . ' ' . $this->size_unit : null;
    }
}
