<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camp;
use Illuminate\Support\Facades\Auth;
use App\Models\ParentModel;

class RegistrationController extends Controller
{
    /**
     * Show the registration form.
     * Accepts optional ?camp=ID to pre-select a camp.
     */
    public function show(Request $request)
    {
        $availableCamps = Camp::getAvailableForRegistration();
        $selectedCampId = $request->query('camp');

        // If the selected camp isn't in the available list, ignore it
        if ($selectedCampId && !$availableCamps->contains('Camp_ID', $selectedCampId)) {
            $selectedCampId = null;
        }

        $parent = null;

        // If user is authenticated, try to load parent info by email
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->email) {
                $parent = ParentModel::where('Email', $user->email)->first();
            }
        }

        return view('registration', compact('availableCamps', 'selectedCampId', 'parent'));
    }
}
