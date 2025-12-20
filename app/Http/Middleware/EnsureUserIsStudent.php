<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensure User Is Student Middleware
 *
 * Restricts access to student-only routes by verifying the authenticated
 * user has the 'student' user_type. Logs all unauthorized access attempts.
 */
class EnsureUserIsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is a student
        if (! $user->isStudent()) {
            // Log unauthorized access attempt
            Log::warning('Non-student attempted to access student route', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'email' => $user->email,
                'ip' => $request->ip(),
                'route' => $request->path(),
                'method' => $request->method(),
            ]);

            abort(403, 'Access denied. This area is for students only.');
        }

        return $next($request);
    }
}
