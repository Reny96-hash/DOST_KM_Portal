<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || auth()->user()->user_role !== 'admin') {
            abort(403, 'Admin access required.');
        }
        return $next($request);
    }
}
