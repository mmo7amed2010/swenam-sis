<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensure User Is Agent Middleware
 *
 * Restricts access to agent-only routes by verifying the authenticated
 * user has the 'agent' user_type. Logs all unauthorized access attempts.
 */
class EnsureUserIsAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is an agent
        if (! $user->isAgent()) {
            // Log unauthorized access attempt
            Log::warning('Non-agent attempted to access agent route', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'email' => $user->email,
                'ip' => $request->ip(),
                'route' => $request->path(),
                'method' => $request->method(),
            ]);

            abort(403, 'Access denied. This area is for agents only.');
        }

        return $next($request);
    }
}
