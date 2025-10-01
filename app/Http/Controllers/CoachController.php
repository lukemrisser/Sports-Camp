<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Coach;
use App\Models\Camp;
use App\Models\User;
use App\Imports\PlayersImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;


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
        $teams = $this->sortTeamsDatabase($players, $numTeams, $campId);
        return $this->exportTeamsToExcel($teams);
    }

    public function exportTeamsToExcel($teams)
    {
        $exportData = [];
        foreach ($teams as $teamIndex => $team) {
            foreach ($team as $playerId) {
                $player = \App\Models\Player::find($playerId);
                $exportData[] = [
                    'Team' => 'Team ' . ($teamIndex + 1),
                    'Player Name' => $player ? ($player->Camper_FirstName . ' ' . $player->Camper_LastName) : 'Unknown',
                    'Age' => $player ? $player->Age : '',
                    'Teammate Requests' => DB::table('Teammate_Request')
                        ->where('Player_ID', $playerId)
                        ->pluck(DB::raw("CONCAT(Requested_FirstName, ' ', Requested_LastName)"))
                        ->implode(', ')
                ];
            }
        }

        $filename = 'teams_export_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($exportData) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
            public function headings(): array { return ['Team', 'Player Name', 'Age']; }
        }, $filename);
    }


    public function sortTeamsDatabase($players, $numTeams, $campId)
    {
        // Get all teammate requests for these players in this camp
        $playerIds = $players->pluck('Player_ID')->toArray();
        $teammateRequests = DB::table('Teammate_Request')
            ->whereIn('Player_ID', $playerIds)
            ->where('Camp_ID', $campId)
            ->get();

        // Build a name-to-id map for players in this camp
        $nameToId = [];
        foreach ($players as $player) {
            $fullName = trim(strtolower($player->Camper_FirstName . ' ' . $player->Camper_LastName));
            $nameToId[$fullName] = $player->Player_ID;
        }

        // Build a map: player_id => array of requested teammate_ids
        $requestMap = [];
        foreach ($teammateRequests as $req) {
            $requestedName = trim(strtolower($req->Requested_FirstName . ' ' . $req->Requested_LastName));
            if (isset($nameToId[$requestedName])) {
                $requestedId = $nameToId[$requestedName];
                // Add both directions
                $requestMap[$req->Player_ID][] = $requestedId;
                $requestMap[$requestedId][] = $req->Player_ID;
            }
        }

        // Helper to recursively collect all connected players for a cluster
        $visited = [];
        $clusters = [];
        foreach ($players as $player) {
            if (in_array($player->Player_ID, $visited)) continue;
            $cluster = $this->collectCluster($player->Player_ID, $requestMap, $visited);
            $clusters[] = $cluster;
        }

        // Calculate max team size
        $totalPlayers = count($players);
        $maxTeamSize = ceil($totalPlayers / $numTeams);

        // Calculate average age for each cluster
        $clusterAges = [];
        foreach ($clusters as $cluster) {
            $ages = [];
            foreach ($cluster as $playerId) {
                $player = $players->where('Player_ID', $playerId)->first();
                if ($player && isset($player->Age)) {
                    $ages[] = $player->Age;
                }
            }
            $clusterAges[] = count($ages) ? array_sum($ages) / count($ages) : 0;
        }

        // Sort clusters by average age
        array_multisort($clusterAges, SORT_ASC, $clusters);

        // Assign clusters (or chunks) to teams
        $teams = array_fill(0, $numTeams, []);
        $teamAges = array_fill(0, $numTeams, 0);
        $teamCounts = array_fill(0, $numTeams, 0);

        foreach ($clusters as $cluster) {
            // Split cluster if it's too large
            $chunks = array_chunk($cluster, $maxTeamSize);
            foreach ($chunks as $chunk) {
                // Calculate chunk average age
                $chunkAges = [];
                foreach ($chunk as $playerId) {
                    $player = $players->where('Player_ID', $playerId)->first();
                    if ($player && isset($player->Age)) {
                        $chunkAges[] = $player->Age;
                    }
                }
                $chunkAvgAge = count($chunkAges) ? array_sum($chunkAges) / count($chunkAges) : 0;

                // Find the team with closest average age (or empty)
                $bestTeam = 0;
                $bestDiff = PHP_INT_MAX;
                for ($i = 0; $i < $numTeams; $i++) {
                    if ($teamCounts[$i] == 0) {
                        $bestTeam = $i;
                        break;
                    }
                    $teamAvgAge = $teamCounts[$i] ? $teamAges[$i] / $teamCounts[$i] : 0;
                    $diff = abs($teamAvgAge - $chunkAvgAge);
                    if ($diff < $bestDiff && ($teamCounts[$i] + count($chunk) <= $maxTeamSize)) {
                        $bestDiff = $diff;
                        $bestTeam = $i;
                    }
                }

                // Assign chunk to best team
                foreach ($chunk as $playerId) {
                    $teams[$bestTeam][] = $playerId;
                    $player = $players->where('Player_ID', $playerId)->first();
                    if ($player && isset($player->Age)) {
                        $teamAges[$bestTeam] += $player->Age;
                    }
                    $teamCounts[$bestTeam]++;
                }
            }
        }

        return $teams;
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
        $user = Auth::user();
        $coach = $user->coach; // assumes User hasOne Coach
        $camps = $coach ? $coach->camps : collect(); // Collection of Camp models or empty

        return view('coach.organize-teams', compact('camps'));
    }
}
