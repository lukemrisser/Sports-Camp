<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Coach;
use App\Models\Camp;
use App\Models\User;
use App\Imports\PlayersImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $numTeams = $request->input('num_teams');
        $data = Excel::toArray(new PlayersImport, $file);
        
        $players = $data[0];

        $this->sortTeamsSpreadsheet($players, $numTeams);
    }

    public function selectCamp(Request $request)
    {
        $campId = $request->input('camp_id');
        $numTeams = $request->input('num_teams');
        $players = Camp::find($campId)->players;
        $this->sortTeamsDatabase($players, $numTeams, $campId);
    }

    public function sortTeamsDatabase($players, $numTeams, $campId)
    {
    // Get all teammate requests for these players in this camp
    $playerIds = $players->pluck('id')->toArray();
    $teammateRequests = DB::table('Teammate_Request')
        ->whereIn('Player_ID', $playerIds)
        ->where('Camp_ID', $campId)
        ->get();

    // Build a name-to-id map for players in this camp
    $nameToId = [];
    foreach ($players as $player) {
        $fullName = trim(strtolower($player->first_name . ' ' . $player->last_name));
        $nameToId[$fullName] = $player->id;
    }

    // Build a map: player_id => array of requested teammate_ids
    $requestMap = [];
    foreach ($teammateRequests as $req) {
        $requestedName = trim(strtolower($req->Teammate_Request));
        if (isset($nameToId[$requestedName])) {
            $requestMap[$req->Player_ID][] = $nameToId[$requestedName];
        }
    }

        // Helper to recursively collect all connected players for a cluster
        $visited = [];
        $clusters = [];
        foreach ($players as $player) {
            if (in_array($player->id, $visited)) continue;
            $cluster = $this->collectCluster($player->id, $requestMap, $visited);
            $clusters[] = $cluster;
        }
        // $clusters is now an array of arrays of player IDs
        // You can map these IDs back to player objects if needed
        // Example: $players->whereIn('id', $cluster)
        return $clusters;
    }

    // Recursively collect all players in a cluster
    protected function collectCluster($playerId, $requestMap, &$visited)
    {
        if (in_array($playerId, $visited)) return [];
        $visited[] = $playerId;
        $cluster = [$playerId];
        if (!empty($requestMap[$playerId])) {
            foreach ($requestMap[$playerId] as $teammateId) {
                if (!in_array($teammateId, $visited)) {
                    $cluster = array_merge($cluster, $this->collectCluster($teammateId, $requestMap, $visited));
                }
            }
        }
        return $cluster;
    }

    public function sortTeamsSpreadsheet($players, $numTeams)
    {
        return;
    }

    public function getCampsForCoach()
    {
        $user = auth()->user(); //Error here for some reason, but works fine
        $coach = $user->coach; 
        $camps = $coach ? $coach->camps : collect();
        return view('coach.organize-teams', compact('camps'));
    }

}
