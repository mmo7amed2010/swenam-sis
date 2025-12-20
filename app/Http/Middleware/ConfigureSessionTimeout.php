<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigureSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $userType = $request->user()->user_type;

            // Calculate timeout in minutes based on user type
            $timeoutMinutes = match ($userType) {
                'student' => 30,      // 30 minutes
                'instructor' => 120,  // 2 hours
                'admin' => 240,       // 4 hours
                default => 120
            };

            // Set session lifetime dynamically (in seconds)
            // This must be done before session is written
            config(['session.lifetime' => $timeoutMinutes]);

            // Also update the session store's lifetime for current session
            $request->session()->put('_session_lifetime', $timeoutMinutes * 60);
        }

        return $next($request);
    }
}
