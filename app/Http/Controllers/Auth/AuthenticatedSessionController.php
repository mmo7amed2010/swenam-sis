<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\LoginLog;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    // Login attempt constants
    private const ADMIN_MAX_ATTEMPTS = 3;

    private const DEFAULT_MAX_ATTEMPTS = 5;

    private const ADMIN_LOCKOUT_MINUTES = 30;

    private const DEFAULT_LOCKOUT_MINUTES = 15;

    // Session timeout constants (in minutes)
    private const STUDENT_SESSION_TIMEOUT = 30;

    private const INSTRUCTOR_SESSION_TIMEOUT = 120;

    private const ADMIN_SESSION_TIMEOUT = 240;

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        addJavascriptFile('assets/js/custom/authentication/sign-in/general.js');

        return view('pages/auth.login');
    }

    /**
     * Detect user role based on email for theme switching.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function detectRole(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            // Don't reveal if email exists (security)
            return response()->json(['role' => null]);
        }

        // Get user's type (student, instructor, admin)
        $userType = $user->user_type;

        return response()->json([
            'role' => $userType,
            'theme' => $this->getThemeForRole($userType),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        // Check if account is locked BEFORE validating credentials
        // This is standard security practice to prevent brute force attacks
        // Note: This may reveal account existence, but it's a necessary tradeoff
        // for effective rate limiting and account protection
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->logLoginAttempt($request, 'locked', null, 'Account locked');

            return $this->sendLockoutResponse($request);
        }

        // Attempt authentication
        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();

            $user = $request->user();

            // Configure role-based session timeout
            $this->configureSessionTimeout($request, $user);

            // Clear failed attempts and update last login
            $this->clearLoginAttempts($request);
            $user->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
                'last_login_ip' => $request->getClientIp(),
            ]);

            // Log successful login
            $this->logLoginAttempt($request, 'success', $user->id);

            // Clear any stored intended URL to prevent cross-user-type redirect issues
            // Each user type has their own dashboard, so always redirect there
            $request->session()->forget('url.intended');

            return redirect(RouteServiceProvider::HOME);
        }

        // Increment failed attempts
        $this->incrementLoginAttempts($request);

        // Log failed attempt
        $this->logLoginAttempt($request, 'failed', null, 'Invalid credentials');

        throw ValidationException::withMessages([
            'email' => __('These credentials do not match our records.'),
        ]);
    }

    /**
     * Attempt to authenticate the user.
     *
     * @return bool
     */
    protected function attemptLogin(LoginRequest $request)
    {
        return Auth::attempt(
            $request->only('email', 'password'),
            $request->filled('remember')
        );
    }

    /**
     * Check if user has too many login attempts.
     *
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        // Skip rate limiting in local environment
        if (app()->environment('local')) {
            return false;
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return false;
        }

        // Check if user is currently locked
        if ($user->locked_until && $user->locked_until->isFuture()) {
            return true;
        }

        // Check failed attempts threshold (admin: 3, others: 5)
        $maxAttempts = $this->getMaxAttempts($user);

        return $user->failed_login_attempts >= $maxAttempts;
    }

    /**
     * Get maximum login attempts for user.
     */
    protected function getMaxAttempts(User $user): int
    {
        return $user->isAdmin()
            ? config('lms.security.admin_max_attempts', self::ADMIN_MAX_ATTEMPTS)
            : config('lms.security.default_max_attempts', self::DEFAULT_MAX_ATTEMPTS);
    }

    /**
     * Get lockout duration in minutes for user.
     */
    protected function getLockoutMinutes(User $user): int
    {
        return $user->isAdmin()
            ? config('lms.security.admin_lockout_minutes', self::ADMIN_LOCKOUT_MINUTES)
            : config('lms.security.default_lockout_minutes', self::DEFAULT_LOCKOUT_MINUTES);
    }

    /**
     * Increment failed login attempts.
     *
     * @return void
     */
    protected function incrementLoginAttempts(Request $request)
    {
        // Skip tracking failed attempts in local environment
        if (app()->environment('local')) {
            return;
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->increment('failed_login_attempts');

            // Lock account if threshold reached
            $maxAttempts = $this->getMaxAttempts($user);
            $lockoutDuration = $this->getLockoutMinutes($user);

            if ($user->failed_login_attempts >= $maxAttempts) {
                $user->update([
                    'locked_until' => now()->addMinutes($lockoutDuration),
                ]);
            }
        }
    }

    /**
     * Clear login attempts for user.
     *
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
        }
    }

    /**
     * Send lockout response.
     *
     * @return void
     */
    protected function sendLockoutResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $seconds = $user->locked_until->diffInSeconds(now());

        throw ValidationException::withMessages([
            'email' => [
                'Too many failed login attempts. Please try again in '.
                ceil($seconds / 60).' minutes.',
            ],
        ]);
    }

    /**
     * Log login attempt.
     *
     * @param  string  $status
     * @param  int|null  $userId
     * @param  string|null  $failureReason
     * @return void
     */
    protected function logLoginAttempt(Request $request, $status, $userId = null, $failureReason = null)
    {
        LoginLog::create([
            'user_id' => $userId,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $status,
            'failure_reason' => $failureReason,
        ]);
    }

    /**
     * Get theme configuration for role.
     *
     * @param  string|null  $role
     * @return array
     */
    protected function getThemeForRole($role)
    {
        return match ($role) {
            'student' => [
                'primary' => '#3b82f6',
                'secondary' => '#10b981',
                'background' => '#f0f9ff',
                'name' => 'student-theme',
            ],
            'instructor' => [
                'primary' => '#1e40af',
                'secondary' => '#d97706',
                'background' => '#fef3c7',
                'name' => 'instructor-theme',
            ],
            'admin' => [
                'primary' => '#1f2937',
                'secondary' => '#6366f1',
                'background' => '#f9fafb',
                'name' => 'admin-theme',
            ],
            default => [
                'primary' => '#64748b',
                'secondary' => '#8b5cf6',
                'background' => '#ffffff',
                'name' => 'default-theme',
            ]
        };
    }

    /**
     * Configure session timeout based on user role.
     *
     * @return void
     */
    protected function configureSessionTimeout(Request $request, User $user)
    {
        $timeoutMinutes = match ($user->user_type) {
            'student' => config('lms.session.student_timeout', self::STUDENT_SESSION_TIMEOUT),
            'instructor' => config('lms.session.instructor_timeout', self::INSTRUCTOR_SESSION_TIMEOUT),
            'admin' => config('lms.session.admin_timeout', self::ADMIN_SESSION_TIMEOUT),
            default => config('lms.session.instructor_timeout', self::INSTRUCTOR_SESSION_TIMEOUT)
        };

        // Set the session lifetime in seconds for the current session
        config(['session.lifetime' => $timeoutMinutes]);

        // Store timeout info in session for middleware to use
        $request->session()->put('session_timeout', $timeoutMinutes * 60);
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
