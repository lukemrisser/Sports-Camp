<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
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
        if (Route::currentRouteName() === 'coach-register') {
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

        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Add email validation based on registration type
        if ($isCoachRegistration) {
            // Coach must use @messiah.edu email and select a team
            $rules['email'] = [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
                'regex:/^[a-zA-Z0-9._%+-]+@messiah\.edu$/i'
            ];
            $rules['team'] = ['required', 'string', 'in:soccer,basketball,baseball,softball,volleyball,tennis,track,swimming,football,lacrosse,all_sports_camp,soccer_camp,basketball_camp,volleyball_camp,tennis_camp,stem_sports_camp,administration,multiple'];
        } else {
            // Parent/player validation...
            $rules['email'] = [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
                function ($attribute, $value, $fail) {
                    if (preg_match('/@messiah\.edu$/i', $value)) {
                        $fail('Please use the coach registration form for @messiah.edu email addresses.');
                    }
                }
            ];
        }

        // Custom error messages
        $messages = [
            'email.regex' => 'Coach registration requires a valid @messiah.edu email address.',
            'team.required' => 'Please select the team or camp you are associated with.',
            'team.in' => 'Please select a valid team or camp from the list.',
        ];

        $request->validate($rules, $messages);

        // Create the user - you'll need to add 'team' to your users table
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        // Only add team if it's a coach registration
        if ($isCoachRegistration) {
            $userData['team'] = $request->team;
        }

        $user = User::create($userData);

        event(new Registered($user));
        Auth::login($user);

        // Redirect based on email domain
        if (preg_match('/@messiah\.edu$/i', $request->email)) {
            return redirect()->route('coach-dashboard');
        }

        return redirect()->route('dashboard');
    }
}
