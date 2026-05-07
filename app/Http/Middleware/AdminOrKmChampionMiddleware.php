<?php

namespace App\Http\Middleware;

use Closure;

class AdminOrKmChampionMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $role = auth()->user()->user_role;
        if ($role !== 'admin' && $role !== 'km_champion') {
            abort(403, 'Only Admin and KM Champions can access this page.');
        }

        return $next($request);
    }
}
