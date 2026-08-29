<?php

namespace App\Tenant;

class TenantManager
{
    protected static ?string $tenantId = null;

    public static function setTenantId(?string $tenantId): void
    {
        static::$tenantId = $tenantId;
    }

    public static function getTenantId(): ?string
    {
        return static::$tenantId;
    }

    public static function hasTenant(): bool
    {
        return !is_null(static::$tenantId);
    }
}
