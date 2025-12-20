<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordReset
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Refresh user from database to get latest password_change_required value
            $user = auth()->user()->fresh();

            if ($user && $user->password_change_required) {
                // Allow access to password reset routes (both GET and POST) and logout
                // Don't redirect if already on the password reset page or submitting the form
                $isPasswordResetRoute = $request->is('password/reset-required*');
                $isLogoutRoute = $request->is('logout');

                if (! $isPasswordResetRoute && ! $isLogoutRoute) {
                    return redirect()->route('password.reset.required')
                        ->with('warning', 'You must change your password before continuing.');
                }
            }
        }

        return $next($request);
    }
}
