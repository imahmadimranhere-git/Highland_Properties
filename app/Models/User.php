<?php

namespace App\Models;

use App\Enums\RoleSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'role_id', 'name', 'email', 'phone', 'password',
        'avatar', 'designation', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',   // never store a plain password
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function teamMember(): HasOne
    {
        return $this->hasOne(TeamMember::class);
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function assignedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'assigned_consultant_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(ConsultantTarget::class);
    }

    public function hasRole(RoleSlug|string $role): bool
    {
        $slug = $role instanceof RoleSlug ? $role->value : $role;

        return $this->role?->slug === $slug;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(RoleSlug::SuperAdmin);
    }

    public function isConsultant(): bool
    {
        return $this->hasRole(RoleSlug::SalesConsultant);
    }

    public function scopeConsultants(Builder $query): Builder
    {
        return $query->whereRelation('role', 'slug', RoleSlug::SalesConsultant->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
