<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\PendingRegistration;
use App\Notifications\PendingRegistrationVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Check if there's a pending registration for this email
        $pendingRegistration = PendingRegistration::where('email', $request->email)
            ->where('expires_at', '>', now())
            ->first();

        if ($pendingRegistration) {
            // Don't redirect immediately - just fail the login with a specific message
            return back()
                ->withInput($request->only('email'))
                ->with('pending_verification', true)
                ->with('pending_email', $request->email)
                ->withErrors([
                    'email' => 'Your email address is not verified. Please verify your email before logging in.'
                ]);
        }

        // Continue with normal authentication
        $request->authenticate();
        $request->session()->regenerate();

        // Redirect based on user type
        if (Auth::user()->isCoach()) {
            return redirect()->intended('/coach-dashboard');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
