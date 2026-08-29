<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Tenant\HasTenantScope;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasTenantScope;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'password',
        'role',
        'skills',
        'availability_status',
        'status',
        'invite_token',
        'invite_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'invite_expires_at' => 'datetime',
            'skills' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    // Role check helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isOrgAdmin(): bool
    {
        return $this->role === 'org_admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isExecutive(): bool
    {
        return $this->role === 'executive';
    }

    public function hasRole(array|string $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }
}
