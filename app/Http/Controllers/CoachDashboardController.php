<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camp;
use App\Models\Coach;


class CoachDashboardController extends Controller
{
    public function coachDashboard(Request $request)
    {
        // Get the currently logged-in coach
        $coach = Coach::first();


        // Get all camps for this coach
        $camps = $coach->camps;

        // If a specific camp was selected
        $selectedCampId = $request->input('camp_id');

        // Default: empty collection of players
        $players = collect();

        if ($selectedCampId) {
            $camp = $camps->where('camp_id', $selectedCampId)->first();

            if ($camp) {
                $players = $camp->players; // Get players for that camp
            }
        }

        // Return the view with the data
        return view('coach.coach-dashboard', compact('camps', 'players', 'selectedCampId'));
    }
}