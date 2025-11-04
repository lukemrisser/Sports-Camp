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
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class CoachController extends Controller
{
    public function showCreateCampForm()
    {
        return view('coach.create-camp');
    }

    public function showEditCampForm()
    {
        $camps = Camp::all();
        return view('coach.edit-camp', compact('camps'));
    }


    public function getCampData($id)
    {
        $camp = Camp::with('discounts')->findOrFail($id);
        return response()->json($camp);
    }

    public function updateCamp(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'registration_open' => 'required|date',
            'registration_close' => 'required|date',
            'price' => 'required|numeric',
            'gender' => 'required|string',
            'min_age' => 'required|numeric',
            'max_age' => 'required|numeric',
            'discount_amount.*' => 'nullable|numeric',
            'discount_date.*' => 'nullable|date'
        ]);

        DB::beginTransaction();
        try {
            $camp = Camp::findOrFail($id);


            $camp->update([
                'Camp_Name' => $validated['name'],
                'Description' => $validated['description'],
                'Start_Date' => $validated['start_date'],
                'End_Date' => $validated['end_date'],
                'Registration_Open' => $validated['registration_open'],
                'Registration_Close' => $validated['registration_close'],
                'Price' => $validated['price'],
                'Camp_Gender' => $validated['gender'],
                'Age_Min' => $validated['min_age'],
                'Age_Max' => $validated['max_age']
            ]);

            // Replace discounts: delete existing and insert provided
            DB::table('Camp_Discount')->where('Camp_ID', $camp->Camp_ID)->delete();

            $discountAmounts = $request->input('discount_amount', []);
            $discountDates = $request->input('discount_date', []);
            $toInsert = [];
            foreach ($discountAmounts as $i => $amount) {
                $date = trim($discountDates[$i] ?? '');
                if ($amount === null && $date === null) continue;
                if ($amount === null xor $date === null) {
                    throw ValidationException::withMessages([
                        'discount' => ['Each discount must include both an amount and a date.'],
                    ]);
                }
                $toInsert[] = [
                    'Camp_ID' => $camp->Camp_ID,
                    'Discount_Date' => $date,
                    'Discount_Amount' => $amount
                ];
            }
            if (!empty($toInsert)) {
                DB::table('Camp_Discount')->insert($toInsert);
            }

            DB::commit();

            return redirect()->route('edit-camp')->with('success', 'Camp updated successfully');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Camp Update Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update camp');
        }
    }

    public function storeCamp(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'registration_open' => 'required|date',
            'registration_close' => 'required|date',
            'price' => 'required|numeric',
            'gender' => 'required|string',
            'min_age' => 'required|numeric',
            'max_age' => 'required|numeric',
            'discount_amount.*' => 'nullable|numeric',
            'discount_date.*' => 'nullable|date'
        ]);

        // if the Camp is created but the discount insertion fails, everything is rolled back.
        DB::beginTransaction();

        try {
            $camp = Camp::create([
                'Camp_Name' => $validated['name'],
                'Description' => $validated['description'],
                'Start_Date' => $validated['start_date'],
                'End_Date' => $validated['end_date'],
                'Registration_Open' => $validated['registration_open'],
                'Registration_Close' => $validated['registration_close'],
                'Price' => $validated['price'],
                'Camp_Gender' => $validated['gender'],
                'Age_Min' => $validated['min_age'],
                'Age_Max' => $validated['max_age']
            ]);

            $discountAmounts = $request->input('discount_amount', []);
            $discountDates = $request->input('discount_date', []);

            $requestsToInsert = [];
            
            foreach ($discountAmounts as $i => $amount) {
                $amount = $amount;
                $date = trim($discountDates[$i] ?? ''); 

                if ($amount == null && $date == null) continue;

                if ($amount == null XOR $date == null) {
                    throw ValidationException::withMessages([
                        'discount' => ['Each discount must include both an amount and a date.'],
                    ]);
                }

                $requestsToInsert[] = [
                    'Camp_ID' => $camp->Camp_ID,
                    'Discount_Date' => $date,
                    'Discount_Amount' => $amount
                ];
            }

            if (!empty($requestsToInsert)) {
                DB::table('Camp_Discount')->insert($requestsToInsert);
            }
            
            DB::commit(); 

            return redirect()->route('create-camp')
                ->with('success', 'Camp created successfully!');

        } catch (ValidationException $e) {
            DB::rollBack(); 
            return redirect()->back()->withInput()->withErrors($e->errors());
        } 
        catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Camp Creation Database Error: ' . $e->getMessage()); 

            return redirect()->back()->withInput()->with('error', 'A critical error occurred while saving camp data. Please try again.');
        }
    }

    public function uploadSpreadsheet(Request $request)
    {
        $request->validate([
            'spreadsheet' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('spreadsheet');
        $numTeams = $request->input('num_teams');

        // Use toCollection so the controller receives the rows synchronously
        $sheets = Excel::toCollection(new PlayersImport(), $file);
        $importedRows = $sheets->first() ?? collect();

        $camp = Camp::create([
            'Camp_Name' => 'Spreadsheet Camp',
        ]);
        $campId = $camp->Camp_ID;

        $createdPlayers = collect();
        foreach ($importedRows as $row) {

            $player = Player::create([
                'Camper_FirstName' => $row['player_first_name'] ?? '',
                'Camper_LastName' => $row['player_last_name'] ?? '',
                //'Age' => $age,
            ]);

            // Attach player to the camp
            $player->camps()->attach($campId);
            $createdPlayers->push($player);

            // Handle teammate requests
            if (!empty($row['teammate_request'])) {
                // Normalize separators to comma
                $normalized = str_replace([';', '.', '|', ' and '], ',', $row['teammate_request']);
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

        $teams = $this->sortTeamsDatabase($createdPlayers, $numTeams, $campId);
        
        // Prepare teams data for display and Excel
        $teamsData = $this->prepareTeamsData($teams, $createdPlayers, $campId);
        
        // Store data in session for display page and Excel download
        session([
            'teams_display_data' => $teamsData,
            'excel_export_data' => $teamsData,
            'delete_after_export' => true,
            'camp_id_for_cleanup' => $campId
        ]);
        
        return redirect()->route('teams-display');
    }

    public function selectCamp(Request $request)
    {
        $campId = $request->input('camp_id');
        $numTeams = $request->input('num_teams');
        $players = Camp::find($campId)->players;
        $teams = $this->sortTeamsDatabase($players, $numTeams, $campId);
        
        // Prepare teams data for display and Excel
        $teamsData = $this->prepareTeamsData($teams, $players, $campId);
        
        // Store data in session for display page and Excel download
        session([
            'teams_display_data' => $teamsData,
            'excel_export_data' => $teamsData,
            'delete_after_export' => false,
            'camp_id_for_cleanup' => $campId
        ]);
        
        return redirect()->route('teams-display');
    }

    public function exportTeamsToExcel($teams, $delete = false, $campId = null)
    {
        $exportData = [];
        foreach ($teams as $teamIndex => $team) {
            foreach ($team as $playerId) {
                $player = Player::find($playerId);
                $exportData[] = [
                    'Team' => 'Team ' . ($teamIndex + 1),
                    'Player Name' => $player ? ($player->Camper_FirstName . ' ' . $player->Camper_LastName) : 'Unknown',
                    //'Age' => $player ? $player->Age : '',
                    'Teammate Requests' => DB::table('Teammate_Request')
                        ->where('Player_ID', $playerId)
                        ->pluck(DB::raw("CONCAT(Requested_FirstName, ' ', Requested_LastName)"))
                        ->implode(', ')
                ];
            }
        }

        if($delete){
            if($campId){
                DB::table('Teammate_Request')->where('Camp_ID', $campId)->delete();
                $playerIds = DB::table('Player_Camp')->where('Camp_ID', $campId)->pluck('Player_ID');
                Player::whereIn('Player_ID', $playerIds)->delete();
                DB::table('Player_Camp')->where('Camp_ID', $campId)->delete();
                Camp::where('Camp_ID', $campId)->delete();
            }
        }

        $filename = 'teams_export_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($exportData) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
            public function headings(): array { return ['Team', 'Player Name', 'Teammate Requests']; }
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

        // Collect all chunks from all clusters
        $allChunks = [];
        $allChunkAges = [];
        foreach ($clusters as $cluster) {
            $chunks = array_chunk($cluster, $maxTeamSize);
            foreach ($chunks as $chunk) {
                $allChunks[] = $chunk;
                // Calculate chunk average age for sorting/assignment
                $chunkAges = [];
                foreach ($chunk as $playerId) {
                    $player = $players->where('Player_ID', $playerId)->first();
                    if ($player && isset($player->Age)) {
                        $chunkAges[] = $player->Age;
                    }
                }
                $allChunkAges[] = count($chunkAges) ? array_sum($chunkAges) / count($chunkAges) : 0;
            }
        }

        // Sort all chunks by size descending (biggest chunks first)
        $chunkSizes = array_map('count', $allChunks);
        array_multisort($chunkSizes, SORT_DESC, $allChunks, $allChunkAges);

        // Assign chunks to teams
        foreach ($allChunks as $chunkIdx => $chunk) {
            $chunkAvgAge = $allChunkAges[$chunkIdx];
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
                // If best team is full, pick a random team with space
                if (count($teams[$bestTeam]) >= $maxTeamSize) {
                    $availableTeams = [];
                    for ($j = 0; $j < $numTeams; $j++) {
                        if (count($teams[$j]) < $maxTeamSize) {
                            $availableTeams[] = $j;
                        }
                    }
                    if (!empty($availableTeams)) {
                        $randomTeam = $availableTeams[array_rand($availableTeams)];
                    } else {
                        // All teams are full, just use bestTeam
                        $randomTeam = $bestTeam;
                    }
                    $targetTeam = $randomTeam;
                } else {
                    $targetTeam = $bestTeam;
                }
                $teams[$targetTeam][] = $playerId;
                $player = $players->where('Player_ID', $playerId)->first();
                if ($player && isset($player->Age)) {
                    $teamAges[$targetTeam] += $player->Age;
                }
                $teamCounts[$targetTeam]++;
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

    public function prepareTeamsData($teams, $players, $campId)
    {
        $teamsData = [];
        foreach ($teams as $teamIndex => $team) {
            foreach ($team as $playerId) {
                $player = Player::find($playerId);
                $teammateRequests = DB::table('Teammate_Request')
                    ->where('Player_ID', $playerId)
                    ->where('Camp_ID', $campId)
                    ->pluck(DB::raw("CONCAT(Requested_FirstName, ' ', Requested_LastName)"))
                    ->implode(', ');
                
                $teamsData[] = [
                    'Team' => 'Team ' . ($teamIndex + 1),
                    'Player Name' => $player ? ($player->Camper_FirstName . ' ' . $player->Camper_LastName) : 'Unknown',
                    'Teammate Requests' => $teammateRequests
                ];
            }
        }
        return $teamsData;
    }

    public function showTeamsDisplay()
    {
        $teamsData = session('teams_display_data', []);
        
        if (empty($teamsData)) {
            return redirect()->route('organize-teams')->with('error', 'No team data available. Please generate teams first.');
        }

        return view('coach.teams-display', compact('teamsData'));
    }

    public function downloadTeamsExcel()
    {
        $teamsData = session('excel_export_data', []);
        $deleteAfterExport = session('delete_after_export', false);
        $campId = session('camp_id_for_cleanup');
        
        if (empty($teamsData)) {
            return redirect()->route('organize-teams')->with('error', 'No team data available for export.');
        }

        // Clean up temporary data if needed (for spreadsheet uploads)
        if ($deleteAfterExport && $campId) {
            DB::table('Teammate_Request')->where('Camp_ID', $campId)->delete();
            $playerIds = DB::table('Player_Camp')->where('Camp_ID', $campId)->pluck('Player_ID');
            Player::whereIn('Player_ID', $playerIds)->delete();
            DB::table('Player_Camp')->where('Camp_ID', $campId)->delete();
            Camp::where('Camp_ID', $campId)->delete();
        }

        $filename = 'teams_export_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($teamsData) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
            public function headings(): array { return ['Team', 'Player Name', 'Teammate Requests']; }
        }, $filename);
    }
}
