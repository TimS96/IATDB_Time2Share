<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !(auth()->user()->is_admin ?? false)) {
            abort(403, 'Alleen voor admins.');
        }
        return $next($request);
    }
}
