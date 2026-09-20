<?php

namespace App\Models;

use App\Enums\InstallmentFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPlan extends Model
{
    protected $fillable = [
        'unit_category_id', 'booking_amount', 'down_payment', 'installment_count',
        'installment_frequency', 'installment_amount', 'possession_charges', 'notes',
    ];

    protected $casts = [
        'installment_frequency' => InstallmentFrequency::class,
        'booking_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'possession_charges' => 'decimal:2',
        'installment_count' => 'integer',
    ];

    public function unitCategory(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class);
    }

    /** Sum of every component, for cross-checking against the category price. */
    public function getPlanTotalAttribute(): float
    {
        return (float) $this->booking_amount
            + (float) $this->down_payment
            + ((float) $this->installment_amount * $this->installment_count)
            + (float) $this->possession_charges;
    }
}
