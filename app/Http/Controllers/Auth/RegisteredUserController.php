<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Coach;
use App\Models\PendingRegistration;
use App\Notifications\PendingRegistrationVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        if (request()->is('coach-register')) {
            return view('auth.coach-register');
        }
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $isCoachRegistration = $request->is('coach-register');

        if ($isCoachRegistration) {
            return $this->storeCoachPending($request);
        }

        // Regular user (parent/player) validation - UPDATED for fname/lname
        $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check if email exists in users table
                    if (User::where('email', $value)->exists()) {
                        $fail('This email is already registered.');
                    }
                    // Check if email exists in pending registrations
                    if (PendingRegistration::where('email', $value)
                        ->where('expires_at', '>', now())
                        ->exists()
                    ) {
                        $fail('This email has a pending registration. Please check your email or wait for it to expire.');
                    }
                    // Messiah.edu emails should use coach registration
                    if (preg_match('/@messiah\.edu$/i', $value)) {
                        $fail('Please use the coach registration form for @messiah.edu email addresses.');
                    }
                }
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create pending registration - store fname and lname in additional_data
        $token = Str::random(64);
        $pendingRegistration = PendingRegistration::create([
            'name' => $request->fname . ' ' . $request->lname, // Store full name for email
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => $token,
            'additional_data' => [
                'fname' => $request->fname,
                'lname' => $request->lname,
            ],
            'expires_at' => now()->addHours(48), // 48 hour expiry
        ]);

        // Send verification email (using full name)
        Notification::route('mail', $request->email)
            ->notify(new PendingRegistrationVerification($token, $request->fname . ' ' . $request->lname, false));

        // Store email in session for display
        session(['pending_email' => $request->email]);

        // Redirect to the Laravel verify-email view
        return redirect()->route('verification.notice');
    }

    /**
     * Handle coach registration (pending).
     */
    private function storeCoachPending(Request $request): RedirectResponse
    {
        // Coach validation - already has firstname/lastname
        $request->validate([
            'coach_firstname' => ['required', 'string', 'max:255'],
            'coach_lastname' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@messiah\.edu$/i',
                function ($attribute, $value, $fail) {
                    // Check users table
                    if (User::where('email', $value)->exists()) {
                        $fail('This email is already registered.');
                    }
                    // Check pending registrations
                    if (PendingRegistration::where('email', $value)
                        ->where('expires_at', '>', now())
                        ->exists()
                    ) {
                        $fail('This email has a pending registration. Please check your email.');
                    }
                    // Check coach relationships
                    $coachExists = Coach::whereHas('user', function ($query) use ($value) {
                        $query->where('email', $value);
                    })->exists();
                    if ($coachExists) {
                        $fail('This email address is already registered.');
                    }
                }
            ],
            'sport' => [
                'required',
                'string',
                'in:soccer,basketball,baseball,softball,volleyball,tennis,track,swimming,football,lacrosse,all_sports_camp,soccer_camp,basketball_camp,volleyball_camp,tennis_camp,stem_sports_camp,administration,multiple'
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'admin' => ['nullable', 'boolean'],
        ], [
            'email.regex' => 'Coach registration requires a valid @messiah.edu email address.',
        ]);

        // Create pending registration with coach data
        $token = Str::random(64);
        $fullName = $request->coach_firstname . ' ' . $request->coach_lastname;

        $pendingRegistration = PendingRegistration::create([
            'name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => $token,
            'additional_data' => [
                'is_coach' => true,
                'fname' => $request->coach_firstname,  // Store as fname
                'lname' => $request->coach_lastname,   // Store as lname
                'coach_firstname' => $request->coach_firstname,
                'coach_lastname' => $request->coach_lastname,
                'sport' => $request->sport,
                'admin' => $request->has('admin') && $request->admin == '1',
            ],
            'expires_at' => now()->addHours(48), // 48 hour expiry
        ]);

        // Send verification email
        Notification::route('mail', $request->email)
            ->notify(new PendingRegistrationVerification($token, $fullName, true));

        // Store email in session for display
        session(['pending_email' => $request->email]);

        // Redirect to the Laravel verify-email view
        return redirect()->route('verification.notice');
    }

    /**
     * Verify registration and create actual user account
     */
    public function verifyRegistration($token): RedirectResponse
    {
        // Find pending registration
        $pendingRegistration = PendingRegistration::where('token', $token)->first();

        if (!$pendingRegistration) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid verification link.']);
        }

        if ($pendingRegistration->hasExpired()) {
            $pendingRegistration->delete();
            return redirect()->route('register')
                ->withErrors(['email' => 'Your verification link has expired. Please register again.']);
        }

        DB::beginTransaction();

        try {
            // Get fname and lname from additional_data
            $additionalData = $pendingRegistration->additional_data;
            $fname = $additionalData['fname'] ?? '';
            $lname = $additionalData['lname'] ?? '';

            // Create user with fname and lname
            $user = User::create([
                'fname' => $fname,
                'lname' => $lname,
                'name' => $pendingRegistration->name, // Keep full name for compatibility
                'email' => $pendingRegistration->email,
                'password' => $pendingRegistration->password,
                'email_verified_at' => now(),
            ]);

            // Create coach record if needed
            if ($additionalData && isset($additionalData['is_coach']) && $additionalData['is_coach']) {
                Coach::create([
                    'Coach_FirstName' => $additionalData['coach_firstname'],
                    'Coach_LastName' => $additionalData['coach_lastname'],
                    'user_id' => $user->id,
                    'admin' => $additionalData['admin'] ?? false,
                    'sport' => $additionalData['sport'],
                ]);
            }

            // Delete pending registration
            $pendingRegistration->delete();

            DB::commit();

            // Log the user in
            Auth::login($user);

            // Force refresh the user instance to ensure all attributes are loaded
            Auth::user()->refresh();

            // Regenerate session
            request()->session()->regenerate();

            // Clear any cached user data
            request()->session()->put('url.intended', null);

            // Redirect based on user type
            if ($user->isCoach()) {
                return redirect()->intended('/coach-dashboard')
                    ->with('success', 'Your account has been successfully created and verified! Welcome to Falcon Teams.');
            }

            return redirect()->intended('/')
                ->with('success', 'Your account has been successfully created and verified! You can now register your children for camps.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to complete registration: ' . $e->getMessage());

            return redirect()->route('register')
                ->withErrors(['email' => 'Registration failed. Please try again or contact support.']);
        }
    }

    public function showResendForm(Request $request): View
    {
        return view('auth.resend-verification', [
            'email' => $request->query('email', '')
        ]);
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $pendingRegistration = PendingRegistration::where('email', $request->email)
            ->where('expires_at', '>', now())
            ->first();

        if ($pendingRegistration) {
            $isCoach = isset($pendingRegistration->additional_data['is_coach']) &&
                $pendingRegistration->additional_data['is_coach'];

            Notification::route('mail', $pendingRegistration->email)
                ->notify(new PendingRegistrationVerification(
                    $pendingRegistration->token,
                    $pendingRegistration->name,
                    $isCoach
                ));

            session(['pending_email' => $pendingRegistration->email]);

            // Redirect to verification notice page
            return redirect()->route('verification.notice')
                ->with('success', 'Verification email sent! Please check your inbox.');
        }

        return back()->withErrors(['email' => 'No pending registration found with this email address. Please register first.']);
    }
}
