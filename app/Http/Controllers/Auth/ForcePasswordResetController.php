<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForcePasswordResetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForcePasswordResetController extends Controller
{
    /**
     * Display the password reset required page.
     */
    public function show(): View
    {
        if (! auth()->check() || ! auth()->user()->password_change_required) {
            return redirect()->route('dashboard');
        }

        return view('pages.auth.force-password-reset');
    }

    /**
     * Handle the password reset update.
     */
    public function update(ForcePasswordResetRequest $request): RedirectResponse
    {
        $user = auth()->user();

        // Validate that new password is different from current password
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The new password must be different from your current temporary password.',
            ]);
        }

        // Update password and clear the flag
        $user->update([
            'password' => Hash::make($request->password),
            'password_change_required' => false,
        ]);

        // Refresh the user in the session to ensure middleware sees the updated value
        auth()->setUser($user->fresh());

        return redirect()->route('dashboard')
            ->with('success', 'Password changed successfully. Welcome to '.config('app.name').'!');
    }
}
