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
        $playersForJs = [];

        // If user is authenticated, try to load parent info by email and related players
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->email) {
                $parent = ParentModel::where('Email', $user->email)
                    ->with('players.camps')
                    ->first();

                if ($parent) {
                    // Prepare a JS-safe array of players and their camp ids for the view
                    $playersForJs = $parent->players->map(function ($p) {
                        return [
                            'Player_ID' => $p->Player_ID,
                            'Camper_FirstName' => $p->Camper_FirstName,
                            'Camper_LastName' => $p->Camper_LastName,
                            'Gender' => $p->Gender,
                            'Birth_Date' => $p->Birth_Date,
                            'Shirt_Size' => $p->Shirt_Size,
                            'Allergies' => $p->Allergies,
                            'Asthma' => $p->Asthma,
                            'Medication_Status' => $p->Medication_Status,
                            'Injuries' => $p->Injuries,
                            'camps' => $p->camps->map(function ($c) {
                                return ['Camp_ID' => $c->Camp_ID];
                            })->toArray()
                        ];
                    })->toArray();
                }
            }
        }

        return view('registration', compact('availableCamps', 'selectedCampId', 'parent', 'playersForJs'));
    }
}
