<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifiedStaged
{
    public function handle(Request $request, Closure $next)
    {
        // If no user, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if email_verified_at is set (all users in staged registration have this)
        if (Auth::user()->email_verified_at === null) {
            // This shouldn't happen in staged registration
            \Log::error('Unverified user found in staged registration system', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email
            ]);

            // Log them out and send to register
            Auth::logout();
            return redirect()->route('register')
                ->withErrors(['email' => 'Please register and verify your email.']);
        }

        return $next($request);
    }
}
