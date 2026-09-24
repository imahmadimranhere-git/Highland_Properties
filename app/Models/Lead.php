<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'society_id', 'unit_category_id', 'plot_category_id', 'assigned_to',
        'name', 'phone', 'email', 'message',
        'status', 'source', 'next_follow_up_at', 'closed_at', 'deal_value',
    ];

    protected $casts = [
        'status' => LeadStatus::class,
        'source' => LeadSource::class,
        'next_follow_up_at' => 'date',
        'closed_at' => 'datetime',
        'deal_value' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function plotCategory(): BelongsTo
    {
        return $this->belongsTo(PlotCategory::class);
    }

    public function unitCategory(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    /**
     * Hard security boundary for the consultant portal: every lead query
     * is scoped here, never by hiding buttons in the view.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isSuperAdmin()
            ? $query
            : $query->where('assigned_to', $user->id);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            LeadStatus::ClosedWon->value,
            LeadStatus::ClosedLost->value,
        ]);
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('next_follow_up_at', today());
    }

    public function getWhatsappLinkAttribute(): string
    {
        return 'https://wa.me/' . preg_replace('/\D+/', '', $this->phone);
    }
}
