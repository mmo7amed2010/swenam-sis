<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckDashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized access to dashboard.');
        }

        // Check valid user_type
        if (in_array($user->user_type, ['student', 'instructor', 'admin'])) {
            Log::info('Dashboard access granted', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'email' => $user->email,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            return $next($request);
        }

        // Log unauthorized access attempt
        Log::warning('Unauthorized dashboard access attempt', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'email' => $user->email,
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
        ]);

        // Deny access if user_type is not valid
        abort(403, 'Unauthorized access to dashboard.');
    }
}
