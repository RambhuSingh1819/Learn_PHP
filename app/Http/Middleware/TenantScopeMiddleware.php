<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Tenant\TenantManager;
use Symfony\Component\HttpFoundation\Response;

class TenantScopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->organization_id) {
            TenantManager::setTenantId(auth()->user()->organization_id);
        }

        return $next($request);
    }
}
