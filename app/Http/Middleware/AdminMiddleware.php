<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Basic admin gate: only allow users with is_admin = true
     */
    public function handle(Request $request, Closure $next)
    {
        // Not logged in or not admin -> 403
        if (!$request->user() || !$request->user()->is_admin) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
