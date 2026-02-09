<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check Suspended User Middleware
 *
 * Blocks suspended users from accessing the system by logging them out
 * and redirecting to the login page. Runs on every web request to ensure
 * suspended users are immediately blocked, even if already logged in.
 */
class CheckSuspendedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isSuspended()) {
            Log::warning('Suspended user attempted to access the system', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'email' => $user->email,
                'ip' => $request->ip(),
                'route' => $request->path(),
                'method' => $request->method(),
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'suspended' => 'Your account has been suspended. Please contact the administrator.',
            ]);
        }

        return $next($request);
    }
}
