<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Coach;
use App\Models\Camp;
use App\Models\User;


class CoachController extends Controller
{


    public function uploadSpreadsheet(Request $request)
    {
        $request->validate([
            'spreadsheet' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('spreadsheet');
        // Parse the file using Excel::toArray() or similar
        // Your logic here
        $this->sortTeams([], 0);
    }

    public function selectCamp(Request $request)
    {
        $campId = $request->input('camp_id');
        // You can now use $campId to load the camp, store it in session, etc.
        // Example: $camp = Camp::find($campId);

        // Redirect or return a view as needed
        $this->sortTeams([], 0);
    }

    public function sortTeams($players, $numTeams)
    {
        return view('home');
    }

    public function getCampsForCoach($coachId)
    {
        $user = auth()->user();
        $coach = $user->coach; // assumes User hasOne Coach
        $camps = $coach ? $coach->camps : collect(); // Collection of Camp models or empty
        return view('coach.organize-teams', compact('camps'));
    }

}