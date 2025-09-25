<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Coach;
use App\Models\Camp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class CoachController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // For now, just show a simple dashboard
        // Later you can add logic to get registrations for this coach's camps
        return view('coach-dashboard', compact('user'));
    }

    public function uploadSpreadsheet(Request $request)
    {
        $request->validate([
            'spreadsheet' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('spreadsheet');

        // Parse the file using Excel::toArray() or similar
        // Your logic here

        // For now, redirect back with success message
        return redirect()->route('coach-dashboard')->with('success', 'Spreadsheet uploaded successfully!');
    }

    public function selectCamp(Request $request)
    {
        $campId = $request->input('camp_id');

        // You can now use $campId to load the camp, store it in session, etc.
        // Example: $camp = Camp::find($campId);
        // Redirect or return a view as needed

        // Store selected camp in session
        session(['selected_camp_id' => $campId]);

        return redirect()->route('coach-dashboard')->with('success', 'Camp selected successfully!');
    }

    public function sortTeams($players, $numTeams)
    {
        // Your team sorting logic will go here
        // For now, just return to home
        return redirect()->route('home');
    }

    public function getCampsForCoach()
    {
        $user = Auth::user();
        $coach = $user->coach; // assumes User hasOne Coach
        $camps = $coach ? $coach->camps : collect(); // Collection of Camp models or empty

        return view('coach.organize-teams', compact('camps'));
    }
}
