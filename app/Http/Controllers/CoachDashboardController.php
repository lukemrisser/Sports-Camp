<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Camp;

class CoachDashboardController extends Controller
{
    public function coachDashboard(Request $request) {

        // $coach = auth()->user()->coach;
        $coach = \App\Models\Coach::first();


        // getting camps for the drop-down menu
        $camps = $coach->camps;

        // If a specific camp was selected
        $selectedCampId = $request->input('camp_id');

        $players = collect();

        if($selectedCampId) {
            $camp = $camps->where('Camp_ID', $selectedCampId)->first();

            if($camp) {
                $players = $camp->players; // Pull the players linked to  that specific camp
            }
        }

        return view('coach.coach-dashboard', compact('camps', 'players', 'selectedCampId'));
    }
}