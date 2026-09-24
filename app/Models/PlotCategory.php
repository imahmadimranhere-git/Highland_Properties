<?php

namespace App\Models;

use App\Enums\PlotType;
use App\Enums\UnitAvailability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlotCategory extends Model
{
    protected $fillable = [
        'society_id', 'size_label', 'area_sqft', 'block', 'plot_type', 'dimensions',
        'price_per_marla', 'total_price', 'availability',
        'total_plots', 'available_plots', 'notes', 'sort_order',
    ];

    protected $casts = [
        'plot_type' => PlotType::class,
        'availability' => UnitAvailability::class,
        'area_sqft' => 'integer',
        'price_per_marla' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }
}
