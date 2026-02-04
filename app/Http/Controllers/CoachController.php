<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Coach;
use App\Models\Camp;
use App\Models\Sport;
use App\Models\User;
use App\Models\Player;
use App\Models\ExtraFee;
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
        $sports = Sport::orderBy('Sport_Name')->get();
        $coach = Auth::user()->coach;
        $defaultSportId = $coach ? $coach->Sport_ID : null;

        return view('coach.create-camp', compact('sports', 'defaultSportId'));
    }

    public function showEditCampForm()
    {
        $camps = Camp::all();
        $sports = Sport::orderBy('Sport_Name')->get();
        return view('coach.edit-camp', compact('camps', 'sports'));
    }


    public function getCampData($id)
    {
        try {
            Log::info("Fetching camp data for ID: {$id}");
            $camp = Camp::with(['discounts', 'extraFees'])->findOrFail($id);
            Log::info("Successfully retrieved camp: {$camp->Camp_Name}");
            return response()->json($camp);
        } catch (\Exception $e) {
            Log::error("Failed to fetch camp data for ID {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Camp not found'], 404);
        }
    }

    public function updateCamp(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_id' => 'required|integer|exists:Sports,Sport_ID',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'registration_open' => 'required|date',
            'registration_close' => 'required|date',
            'price' => 'required|numeric',
            'gender' => 'required|string|in:coed,boys,girls',
            'min_age' => 'required|numeric',
            'max_age' => 'required|numeric',
            'max_capacity' => 'required|integer|min:1|max:1000',
            'location_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'zip_code' => 'required|string|regex:/^[0-9]{5}(-[0-9]{4})?$/',
            'discount_amount.*' => 'nullable|numeric',
            'discount_date.*' => 'nullable|date',
            'promo_code.*' => 'nullable|string',
            'promo_amount.*' => 'nullable|numeric',
            'promo_date.*' => 'nullable|date',
            'extra_fee_name.*' => 'nullable|string|max:100',
            'extra_fee_description.*' => 'nullable|string',
            'extra_fee_amount.*' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $camp = Camp::findOrFail($id);


            // Normalize gender to lowercase
            $normalizedGender = strtolower($validated['gender']);

            $camp->update([
                'Sport_ID' => $validated['sport_id'],
                'Camp_Name' => $validated['name'],
                'Description' => $validated['description'],
                'Start_Date' => $validated['start_date'],
                'End_Date' => $validated['end_date'],
                'Registration_Open' => $validated['registration_open'],
                'Registration_Close' => $validated['registration_close'],
                'Price' => $validated['price'],
                'Camp_Gender' => $normalizedGender,
                'Age_Min' => $validated['min_age'],
                'Age_Max' => $validated['max_age'],
                'Max_Capacity' => $validated['max_capacity'],
                'Location_Name' => $validated['location_name'],
                'Street_Address' => $validated['street_address'],
                'City' => $validated['city'],
                'State' => $validated['state'],
                'Zip_Code' => $validated['zip_code']
            ]);

            // Replace discounts: delete existing and insert provided
            DB::table('Camp_Discount')->where('Camp_ID', $camp->Camp_ID)->delete();

            $toInsert = [];

            // Early registration discounts (amount + date required)
            $discountAmounts = $request->input('discount_amount', []);
            $discountDates = $request->input('discount_date', []);
            foreach ($discountAmounts as $i => $amount) {
                $date = trim($discountDates[$i] ?? '');

                if ($amount == null && $date == null) continue;

                if ($amount == null || $date == null) {
                    throw ValidationException::withMessages([
                        'discount' => ['Each discount must include both an amount and a date.'],
                    ]);
                }

                $toInsert[] = [
                    'Camp_ID' => $camp->Camp_ID,
                    'Discount_Date' => $date,
                    'Discount_Amount' => $amount,
                    'Promo_Code' => null,
                ];
            }

            // Promo codes (code + amount required, date optional)
            $promoCodes = $request->input('promo_code', []);
            $promoAmounts = $request->input('promo_amount', []);
            $promoDates = $request->input('promo_date', []);
            foreach ($promoCodes as $i => $promoCode) {
                $promoCode = trim($promoCode);
                $promoAmount = $promoAmounts[$i] ?? null;
                $promoDate = trim($promoDates[$i] ?? '');

                // Skip completely empty rows
                if (!$promoCode && !$promoAmount && !$promoDate) continue;

                // Only validate if there's at least some data entered
                if ($promoCode || $promoAmount) {
                    if (!$promoCode || !$promoAmount) {
                        throw ValidationException::withMessages([
                            'promo' => ['Each promo code must have both a code and amount.'],
                        ]);
                    }

                    $toInsert[] = [
                        'Camp_ID' => $camp->Camp_ID,
                        'Promo_Code' => $promoCode,
                        'Discount_Amount' => $promoAmount,
                        'Discount_Date' => $promoDate ?: null,
                    ];
                }
            }

            if (!empty($toInsert)) {
                DB::table('Camp_Discount')->insert($toInsert);
            }

            // Replace extra fees: delete existing and insert provided
            ExtraFee::where('Camp_ID', $camp->Camp_ID)->delete();

            $extraNames = $request->input('extra_fee_name', []);
            $extraAmounts = $request->input('extra_fee_amount', []);
            $extraDescriptions = $request->input('extra_fee_description', []);
            $feeRows = [];

            foreach ($extraNames as $i => $feeName) {
                $name = trim($feeName ?? '');
                $amount = $extraAmounts[$i] ?? null;
                $description = trim($extraDescriptions[$i] ?? '');

                // Skip completely empty rows
                if ($name === '' && ($amount === null || $amount === '')) {
                    continue;
                }

                if ($name === '' || $amount === null || $amount === '') {
                    throw ValidationException::withMessages([
                        'extra_fee' => ['Each extra fee must include both a name and amount.'],
                    ]);
                }

                $feeRows[] = [
                    'Camp_ID' => $camp->Camp_ID,
                    'Fee_Name' => $name,
                    'Fee_Description' => $description ?: null,
                    'Fee_Amount' => $amount,
                ];
            }

            if (!empty($feeRows)) {
                ExtraFee::insert($feeRows);
            }

            DB::commit();

            return redirect()->route('edit-camp')->with('success', 'Camp updated successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            $errorMessage = 'Error updating camp: ';
            $allErrors = $e->errors();
            if (!empty($allErrors)) {
                $errorList = [];
                foreach ($allErrors as $field => $messages) {
                    $errorList[] = implode(', ', $messages);
                }
                $errorMessage .= implode(' | ', $errorList);
            } else {
                $errorMessage .= 'Unknown validation error';
            }
            return redirect()->route('organize-teams')->with('error', $errorMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Camp Update Error: ' . $e->getMessage());
            return redirect()->route('organize-teams')
                ->with('error', 'Failed to update camp: ' . $e->getMessage());
        }
    }

    public function storeCamp(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_id' => 'required|integer|exists:Sports,Sport_ID',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'registration_open' => 'required|date',
            'registration_close' => 'required|date',
            'price' => 'required|numeric',
            'gender' => 'required|string|in:coed,boys,girls',
            'min_age' => 'required|numeric',
            'max_age' => 'required|numeric',
            'max_capacity' => 'required|integer|min:1|max:1000',
            'location_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'zip_code' => 'required|string|regex:/^[0-9]{5}(-[0-9]{4})?$/',
            'discount_amount.*' => 'nullable|numeric',
            'discount_date.*' => 'nullable|date',
            'promo_code.*' => 'nullable|string',
            'promo_amount.*' => 'nullable|numeric',
            'promo_date.*' => 'nullable|date',
            'extra_fee_name.*' => 'nullable|string|max:100',
            'extra_fee_description.*' => 'nullable|string',
            'extra_fee_amount.*' => 'nullable|numeric|min:0'
        ]);

        // if the Camp is created but the discount insertion fails, everything is rolled back.
        DB::beginTransaction();

        try {
            // Normalize gender to lowercase
            $normalizedGender = strtolower($validated['gender']);

            $camp = Camp::create([
                'Sport_ID' => $validated['sport_id'],
                'Camp_Name' => $validated['name'],
                'Description' => $validated['description'],
                'Start_Date' => $validated['start_date'],
                'End_Date' => $validated['end_date'],
                'Registration_Open' => $validated['registration_open'],
                'Registration_Close' => $validated['registration_close'],
                'Price' => $validated['price'],
                'Camp_Gender' => $normalizedGender,
                'Age_Min' => $validated['min_age'],
                'Age_Max' => $validated['max_age'],
                'Max_Capacity' => $validated['max_capacity'],
                'Location_Name' => $validated['location_name'],
                'Street_Address' => $validated['street_address'],
                'City' => $validated['city'],
                'State' => $validated['state'],
                'Zip_Code' => $validated['zip_code']
            ]);

            $discountAmounts = $request->input('discount_amount', []);
            $discountDates = $request->input('discount_date', []);

            $requestsToInsert = [];

            // Handle early registration discounts (amount + date required)
            foreach ($discountAmounts as $i => $amount) {
                $date = trim($discountDates[$i] ?? '');

                if ($amount == null && $date == null) continue;

                if ($amount == null xor $date == null) {
                    throw ValidationException::withMessages([
                        'discount' => ['Each discount must include both an amount and a date.'],
                    ]);
                }

                $requestsToInsert[] = [
                    'Camp_ID' => $camp->Camp_ID,
                    'Discount_Date' => $date,
                    'Discount_Amount' => $amount,
                    'Promo_Code' => null
                ];
            }

            // Handle promo codes (code + amount required, date optional)
            $promoCodes = $request->input('promo_code', []);
            $promoAmounts = $request->input('promo_amount', []);
            $promoDates = $request->input('promo_date', []);

            foreach ($promoCodes as $i => $promoCode) {
                $promoCode = trim($promoCode);
                $promoAmount = $promoAmounts[$i] ?? null;
                $promoDate = trim($promoDates[$i] ?? '');

                // Skip completely empty rows
                if (!$promoCode && !$promoAmount && !$promoDate) continue;

                // Only validate if there's at least some data entered
                if ($promoCode || $promoAmount) {
                    if (!$promoCode || !$promoAmount) {
                        throw ValidationException::withMessages([
                            'promo' => ['Each promo code must have both a code and amount.'],
                        ]);
                    }

                    $requestsToInsert[] = [
                        'Camp_ID' => $camp->Camp_ID,
                        'Promo_Code' => $promoCode,
                        'Discount_Amount' => $promoAmount,
                        'Discount_Date' => $promoDate ?: null
                    ];
                }
            }

            if (!empty($requestsToInsert)) {
                DB::table('Camp_Discount')->insert($requestsToInsert);
            }

            // Handle extra fees
            $extraNames = $request->input('extra_fee_name', []);
            $extraAmounts = $request->input('extra_fee_amount', []);
            $extraDescriptions = $request->input('extra_fee_description', []);
            $feeRows = [];

            foreach ($extraNames as $i => $feeName) {
                $name = trim($feeName ?? '');
                $amount = $extraAmounts[$i] ?? null;
                $description = trim($extraDescriptions[$i] ?? '');

                // Skip completely empty rows
                if ($name === '' && ($amount === null || $amount === '')) {
                    continue;
                }

                if ($name === '' || $amount === null || $amount === '') {
                    throw ValidationException::withMessages([
                        'extra_fee' => ['Each extra fee must include both a name and amount.'],
                    ]);
                }

                $feeRows[] = [
                    'Camp_ID' => $camp->Camp_ID,
                    'Fee_Name' => $name,
                    'Fee_Description' => $description ?: null,
                    'Fee_Amount' => $amount,
                ];
            }

            if (!empty($feeRows)) {
                ExtraFee::insert($feeRows);
            }

            DB::commit();

            return redirect()->route('create-camp')
                ->with('success', 'Camp created successfully!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Camp Creation Database Error: ' . $e->getMessage());

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
            $birthDate = null;
            if (!empty($row['player_birth_date'])) {
                try {
                    if (is_numeric($row['player_birth_date'])) {
                        $excelEpoch = \Carbon\Carbon::create(1900, 1, 1)->subDays(2);
                        $birthDate = $excelEpoch->addDays((int)$row['player_birth_date'])->format('Y-m-d');
                    } else {
                        $birthDate = \Carbon\Carbon::createFromFormat('m/d/Y', $row['player_birth_date'])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    Log::error('Date parsing error for player birth date: ' . $row['player_birth_date'] . ' - ' . $e->getMessage());
                    $birthDate = null;
                }
            }

            $player = Player::create([
                'Camper_FirstName' => $row['player_first_name'] ?? '',
                'Camper_LastName' => $row['player_last_name'] ?? '',
                'Birth_Date' => $birthDate,
            ]);

            // Attach player to the camp
            $player->camps()->attach($campId);
            $createdPlayers->push($player);

            // Handle teammate requests
            if (!empty($row['teammate_request'])) {
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

        $teamsData = $this->prepareTeamsData($teams, $createdPlayers, $campId);

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
        $teamsData = $this->prepareTeamsData($teams, $players, $campId);

        session([
            'teams_display_data' => $teamsData,
            'excel_export_data' => $teamsData,
            'delete_after_export' => false,
            'camp_id_for_cleanup' => $campId
        ]);

        return redirect()->route('teams-display');
    }

    public function sortTeamsDatabase($players, $numTeams, $campId)
    {
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

    public function getCampsForCoach()
    {
        $user = Auth::user();
        $coach = $user->coach;
        $camps = $coach && $coach->Sport_ID
            ? Camp::where('Sport_ID', $coach->Sport_ID)->get()
            : collect();

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
                    'Age' => $player ? $player->Age : '',
                    'Teammate Requests' => $teammateRequests
                ];
            }
        }
        return $teamsData;
    }

    public function showTeamsDisplay()
    {
        $teamsData = session('teams_display_data', []);
        $deleteAfterExport = session('delete_after_export', false);
        $campId = session('camp_id_for_cleanup');

        if (empty($teamsData)) {
            return redirect()->route('organize-teams')->with('error', 'No team data available. Please generate teams first.');
        }

        // Clean up temporary data if it came from spreadsheet upload
        if ($deleteAfterExport && $campId) {
            DB::table('Teammate_Request')->where('Camp_ID', $campId)->delete();
            $playerIds = DB::table('Player_Camp')->where('Camp_ID', $campId)->pluck('Player_ID');
            Player::whereIn('Player_ID', $playerIds)->delete();
            DB::table('Player_Camp')->where('Camp_ID', $campId)->delete();
            Camp::where('Camp_ID', $campId)->delete();

            // Clear the cleanup flags from session since we've already cleaned up
            session()->forget(['delete_after_export', 'camp_id_for_cleanup']);
        }

        return view('coach.teams-display', compact('teamsData'));
    }

    public function downloadTeamsExcel()
    {
        $teamsData = session('excel_export_data', []);

        if (empty($teamsData)) {
            return redirect()->route('organize-teams')->with('error', 'No team data available for export.');
        }

        $filename = 'teams_export_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($teamsData) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data)
            {
                $this->data = $data;
            }
            public function collection()
            {
                return collect($this->data);
            }
            public function headings(): array
            {
                return ['Team', 'Player Name', 'Age', 'Teammate Requests'];
            }
        }, $filename);
    }

    public function selectCampForEmail(Request $request)
    {
        $user = Auth::user();
        $coach = $user->coach;

        if (!$coach) {
            return redirect()->route('home')->with('error', 'You must be a coach to send mass emails.');
        }

        $allCamps = Camp::where('Sport_ID', $coach->Sport_ID)->get();
        $today = now()->toDateString();

        // Separate camps by date and format with dates
        $pastCamps = $allCamps->filter(function ($camp) use ($today) {
            return $camp->End_Date < $today;
        })->map(function ($camp) {
            return [
                'id' => $camp->Camp_ID,
                'name' => $camp->Camp_Name,
                'start_date' => $camp->Start_Date->format('m/d/Y'),
                'end_date' => $camp->End_Date->format('m/d/Y')
            ];
        })->values()->toArray();

        $liveCamps = $allCamps->filter(function ($camp) use ($today) {
            return $camp->Start_Date <= $today && $camp->End_Date >= $today;
        })->map(function ($camp) {
            return [
                'id' => $camp->Camp_ID,
                'name' => $camp->Camp_Name,
                'start_date' => $camp->Start_Date->format('m/d/Y'),
                'end_date' => $camp->End_Date->format('m/d/Y')
            ];
        })->values()->toArray();

        $upcomingCamps = $allCamps->filter(function ($camp) use ($today) {
            return $camp->Start_Date > $today;
        })->map(function ($camp) {
            return [
                'id' => $camp->Camp_ID,
                'name' => $camp->Camp_Name,
                'start_date' => $camp->Start_Date->format('m/d/Y'),
                'end_date' => $camp->End_Date->format('m/d/Y')
            ];
        })->values()->toArray();

        $campStatusOptions = [
            'past' => 'Past Camps',
            'live' => 'Live Camps',
            'upcoming' => 'Upcoming Camps'
        ];

        return view('admin.mass-emails', compact('pastCamps', 'liveCamps', 'upcomingCamps', 'campStatusOptions'));
    }

    public function sendMassEmails(Request $request)
    {
        $validated = $request->validate([
            'camp_id' => 'required|array|min:1',
            'camp_id.*' => 'integer|exists:Camps,Camp_ID',
            'camp_status' => 'required|string|in:past,live,upcoming',
            'subject' => 'required|string|max:255',
            'greeting' => 'nullable|string|max:255',
            'message' => 'required|string',
            'closing' => 'nullable|string|max:255',
        ]);

        // Log inputs to help diagnose failures during preparation
        Log::debug('Mass email inputs', [
            'camp_id_count' => isset($validated['camp_id']) ? count($validated['camp_id']) : 0,
            'camp_ids' => $validated['camp_id'] ?? [],
            'camp_status' => $validated['camp_status'] ?? null,
            'subject_length' => isset($validated['subject']) ? strlen($validated['subject']) : 0
        ]);

        try {
            $user = Auth::user();
            $coach = $user->coach;

            if (!$coach) {
                return redirect()->route('home')->with('error', 'You must be a coach to send mass emails.');
            }

            // Get all selected camps and verify they belong to coach's sport
            $camps = Camp::whereIn('Camp_ID', $validated['camp_id'])->get();

            foreach ($camps as $camp) {
                if ($camp->Sport_ID !== $coach->Sport_ID) {
                    return redirect()->back()->with('error', 'You do not have permission to send emails for one or more selected camps.');
                }
            }

            // Get all parents for the selected camps based on status
            $now = now();
            // Build query using correct table/column names and join Parents via Players.Parent_ID
            $query = DB::table('Player_Camp')
                ->join('Players', 'Player_Camp.Player_ID', '=', 'Players.Player_ID')
                ->join('Parents', 'Players.Parent_ID', '=', 'Parents.Parent_ID')
                ->join('Camps', 'Player_Camp.Camp_ID', '=', 'Camps.Camp_ID')
                ->whereIn('Player_Camp.Camp_ID', $validated['camp_id'])
                ->distinct()
                ->select('Parents.Email as Email', 'Parents.Parent_FirstName as First_Name', 'Parents.Parent_LastName as Last_Name', 'Camps.Camp_Name as Camp_Name');

            $nowDate = $now->toDateString();
            Log::debug('Mass email status filter: ' . $validated['camp_status'] . ', now=' . $nowDate);
            if ($validated['camp_status'] === 'past') {
                $query->where('Camps.End_Date', '<', $nowDate);
            } elseif ($validated['camp_status'] === 'live') {
                $query->where('Camps.Start_Date', '<=', $nowDate)
                    ->where('Camps.End_Date', '>=', $nowDate);
            } elseif ($validated['camp_status'] === 'upcoming') {
                $query->where('Camps.Start_Date', '>', $nowDate);
            }

            try {
                Log::debug('Mass email parent query SQL: ' . $query->toSql(), ['bindings' => $query->getBindings()]);
                $parents = $query->get();
                Log::debug('Mass email parent count: ' . $parents->count());

                if ($parents->isEmpty()) {
                    return redirect()->back()->with('warning', 'No parents found for the selected camp and status.');
                }
            } catch (\Exception $e) {
                Log::error('Mass Email Query Error: ' . $e->getMessage(), ['sql' => $query->toSql(), 'bindings' => $query->getBindings(), 'exception' => $e]);
                return redirect()->back()->withInput()->with('error', 'An error occurred while preparing emails. Please check logs.');
            }

            // Send emails to all parents
            $failedEmails = [];
            foreach ($parents as $parent) {
                try {
                    // Use Laravel's Mail facade to send emails
                    \Illuminate\Support\Facades\Mail::send('emails.mass-email', [
                        'subject' => $validated['subject'],
                        'greeting' => $validated['greeting'] ?? 'Hello',
                        'emailBody' => $validated['message'],
                        'closing' => $validated['closing'] ?? 'Best regards,',
                        'parentName' => $parent->First_Name . ' ' . $parent->Last_Name,
                        'campName' => $parent->Camp_Name,
                        'coachName' => $user->name,
                    ], function ($mail) use ($parent, $validated) {
                        $mail->to($parent->Email)
                            ->subject($validated['subject'])
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    });
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$parent->Email}: " . $e->getMessage());
                    $failedEmails[] = $parent->Email . ' (' . $e->getMessage() . ')';
                }
            }

            // Prepare success message
            $successCount = count($parents) - count($failedEmails);
            $message = "Mass email sent successfully to {$successCount} parent(s).";

            if (!empty($failedEmails)) {
                $message .= " Failed to send to: " . implode(', ', $failedEmails);
            }

            return redirect()->route('select-camp-for-email')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Mass Email Send Error: ' . $e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'An error occurred while sending emails. Please try again.');
        }
    }
}
