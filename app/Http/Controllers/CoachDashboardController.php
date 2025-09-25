<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Camp;

class CoachDashboardController extends Controller
{
    public function index(Request $request) {

        // getting camps for the drop-down menu
        $camps = Camp::all();

        // If a specific camp was selected
        $selectedCampId = $request->input('camp_id');

        $players = collect();

        if($selectedCampId) {
            $camp = Camp::find($selectedCampId);

            if($camp) {
                $players = $camp->players; // Pull the players linked to  that specific camp
            }
        }

        return view('coach-dashboard', compact('camps', 'players', 'selectedCampId'));
    }
}