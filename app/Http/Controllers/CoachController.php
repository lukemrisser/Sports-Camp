<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Coach;
use App\Models\Camp;
use App\Models\User;
use App\Models\Player;
use App\Imports\PlayersImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;


class CoachController extends Controller
{
    public function uploadSpreadsheet(Request $request)
    {
        $request->validate([
            'spreadsheet' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('spreadsheet');
        $numTeams = $request->input('num_teams');
        $data = Excel::toArray(new PlayersImport, $file);

        $players = $data[0];

        $importedPlayers = $data[0];
        $camp = Camp::create([
            'Camp_Name' => 'Spreadsheet Camp',
        ]);
        $campId = $camp->Camp_ID;

        foreach ($importedPlayers as $row) {
            $player = Player::create([
                'Camper_FirstName' => $row['Player First Name'],
                'Camper_LastName' => $row['Player Last Name'],
                'Age' => isset($row['age']) ? (int)$row['Player Age'] : null,
            ]);
            // Attach player to the camp
            $player->camps()->attach($campId);

            // Handle teammate requests
            if (!empty($row['Teammate Request'])) {
                // Normalize separators to comma
                $normalized = str_replace([';', '.', '|', ' and '], ',', $row['Teammate Request']);
                $requests = array_filter(array_map('trim', explode(',', $normalized)));

                foreach ($requests as $requestName) {
                    // Try to split into first and last name
                    $nameParts = preg_split('/\s+/', $requestName);
                    if (count($nameParts) >= 2) {
                        $firstName = $nameParts[0];
                        $lastName = $nameParts[1];
                    } else {
                        $firstName = $nameParts[0];
                        $lastName = '';
                    }
                    DB::table('Teammate_Request')->insert([
                        'Player_ID' => $player->Player_ID,
                        'Requested_FirstName' => $firstName,
                        'Requested_LastName' => $lastName,
                        'Camp_ID' => $campId,
                    ]);
                }
            }
        }

        $teams = $this->sortTeamsDatabase($importedPlayers, $numTeams, $campId);

        DB::table('Teammate_Request')->where('Camp_ID', $campId)->delete();
        $playerIds = DB::table('Camp_Player')->where('Camp_ID', $campId)->pluck('Player_ID');
        Player::whereIn('Player_ID', $playerIds)->delete();
        DB::table('Camp_Player')->where('Camp_ID', $campId)->delete();
        Camp::where('Camp_ID', $campId)->delete();

        return $this->exportTeamsToExcel($teams);
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
                $player = Player::find($playerId);
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
