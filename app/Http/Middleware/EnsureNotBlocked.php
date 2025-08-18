<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        // Temporary log to prove execution
        \Log::info('EnsureNotBlocked hit', [
            'path'    => $request->path(),
            'user_id' => optional($request->user())->id,
        ]);

        $user = $request->user();

        if ($user && $user->blocked_at !== null) {
            // Definitive logout flow
            auth('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Log::info('EnsureNotBlocked hit', ['path' => $request->path(), 'user_id' => optional($request->user())->id]);


            return redirect()->route('login')->with('status', 'Je account is geblokkeerd.');
        }

        return $next($request);
    }
}
