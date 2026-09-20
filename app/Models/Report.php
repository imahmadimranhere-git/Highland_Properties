<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'project_id', 'title', 'type', 'report_date',
        'period_start', 'period_end', 'amount', 'description',
        'attachment_path', 'status', 'reviewed_by', 'reviewed_at', 'review_note',
    ];

    protected $casts = [
        'type' => ReportType::class,
        'status' => ReportStatus::class,
        'report_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'reviewed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** Consultants only ever see their own reports. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isSuperAdmin()
            ? $query
            : $query->where('user_id', $user->id);
    }

    public function isEditable(): bool
    {
        return $this->status === ReportStatus::Draft;
    }
}
