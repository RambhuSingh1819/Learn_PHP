<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasTenantScope
{
    public static function bootHasTenantScope(): void
    {
        // Automatically inject tenant ID on model creation
        static::creating(function (Model $model) {
            if (TenantManager::hasTenant() && !$model->organization_id) {
                $model->organization_id = TenantManager::getTenantId();
            }
        });

        // Apply global tenant scope for queries
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (TenantManager::hasTenant()) {
                $builder->where($builder->getModel()->getTable() . '.organization_id', TenantManager::getTenantId());
            }
        });
    }
}
