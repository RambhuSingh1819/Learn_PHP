<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Tenant\HasTenantScope;

class Task extends Model
{
    use HasFactory, HasUuids, HasTenantScope;

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'assigned_to',
        'created_by',
        'priority',
        'status',
        'due_date',
        'sla_hours',
        'sla_breached_at',
        'escalation_level',
        'closure_comment',
        'proof_attachment_path',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'sla_breached_at' => 'datetime',
        'escalation_level' => 'integer',
        'sla_hours' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
