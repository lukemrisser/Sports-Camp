<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Coach;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Check if this is the coach registration route
        if (request()->is('coach-register')) {
            return view('auth.coach-register');
        }

        // Default to regular registration view
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if this is coming from the coach registration route
        $isCoachRegistration = $request->is('coach-register');

        if ($isCoachRegistration) {
            return $this->storeCoach($request);
        }

        // Regular user registration (parents/players)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (preg_match('/@messiah\.edu$/i', $value)) {
                        $fail('Please use the coach registration form for @messiah.edu email addresses.');
                    }
                }
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Send verification email
        event(new Registered($user));

        // Log the user in (required for verification routes to work)
        Auth::login($user);

        // Redirect to verification notice instead of dashboard
        return redirect()->route('verification.notice');
    }

    /**
     * Handle coach registration.
     */
    private function storeCoach(Request $request): RedirectResponse
    {
        // Validation rules for coach registration
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
                    // Check if email exists in users table
                    $userExists = User::where('email', $value)->exists();

                    // Check if email exists via coach relationships
                    $coachExists = Coach::whereHas('user', function ($query) use ($value) {
                        $query->where('email', $value);
                    })->exists();

                    if ($userExists || $coachExists) {
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
            'sport.required' => 'Please select the sport or camp you are associated with.',
            'sport.in' => 'Please select a valid sport or camp from the list.',
            'coach_firstname.required' => 'First name is required.',
            'coach_lastname.required' => 'Last name is required.',
        ]);

        // Use database transaction to ensure both records are created
        DB::beginTransaction();

        try {
            // Create user record for authentication
            $user = User::create([
                'name' => $request->coach_firstname . ' ' . $request->coach_lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Get admin value from checkbox
            $isAdmin = $request->has('admin') && $request->admin == '1';

            // Create coach record
            Coach::create([
                'Coach_FirstName' => $request->coach_firstname,
                'Coach_LastName' => $request->coach_lastname,
                'user_id' => $user->id,
                'admin' => $isAdmin,
                'sport' => $request->sport,
            ]);

            DB::commit();

            // Send verification email
            event(new Registered($user));

            // Log the coach in (required for verification routes to work)
            Auth::login($user);

            // Redirect to verification notice instead of dashboard
            return redirect()->route('verification.notice');

        } catch (\Exception $e) {
            DB::rollback();

            // Log the error if needed
            \Log::error('Coach registration failed: ' . $e->getMessage());

            return back()
                ->withErrors(['email' => 'Registration failed. Please try again.'])
                ->withInput();
        }
    }
}
