<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Camp;
use App\Models\Coach;
use App\Models\Player;

class CoachDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Keep your existing dashboard method EXACTLY as is
        // Get the currently logged-in user
        $user = Auth::user();

        // Get the coach record for this user
        $coach = $user->coach;

        // Check if user actually has a coach record
        if (!$coach) {
            return redirect('/dashboard')->with('error', 'Coach record not found.');
        }

        // Get all camps (you can filter by coach's sport if needed)
        $camps = Camp::all();
        // Or if you want to filter by sport:
        // $camps = Camp::where('sport', $coach->sport)->get();

        // Get selected camp ID from request
        $selectedCampId = $request->input('camp_id');

        // Initialize players as empty collection
        $players = collect();

        if ($selectedCampId) {
            // Get players for the selected camp
            $camp = Camp::find($selectedCampId);
            if ($camp) {
                // Assuming Player model has Camp_ID field
                $players = Player::where('Camp_ID', $selectedCampId)->get();
                // Or if you have relationships set up:
                // $players = $camp->players;
            }
        }

        // Return the view with all necessary data
        return view('coach.coach-dashboard', compact('user', 'coach', 'camps', 'players', 'selectedCampId'));
    }

    // ADD this new method for camp registrations
    public function campRegistrations(Request $request)
    {
        // Get the currently logged-in user
        $user = Auth::user();

        // Get the coach record for this user
        $coach = $user->coach;

        // Check if user actually has a coach record
        if (!$coach) {
            return redirect('/dashboard')->with('error', 'Coach record not found.');
        }

        // Get all camps - add debugging
        $camps = Camp::all();
        Log::info('Camps found: ' . $camps->count());
        Log::info('Camps data: ' . $camps->toJson());

        // Get selected camp ID from request
        $selectedCampId = $request->input('camp_id');

        // Initialize players as empty collection
        $players = collect();

        if ($selectedCampId) {
            // Get the camp with its players through the many-to-many relationship
            $camp = Camp::find($selectedCampId);
            if ($camp) {
                // Use the relationship to get players for this camp
                $players = $camp->players;
            }
        }

        // Return the camp-registrations view
        return view('coach.camp-registrations', compact('user', 'coach', 'camps', 'players', 'selectedCampId'));
    }
}
