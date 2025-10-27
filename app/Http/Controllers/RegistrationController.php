<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camp;

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

        return view('registration', compact('availableCamps', 'selectedCampId'));
    }
}
