<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ParentModel;
use App\Models\Camp;
use App\Models\Player;
use App\Models\ExtraFee;

class PlayerController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            //'Division_Name' => 'nullable|string|max:50',
            'Camp_ID' => 'required|integer|exists:Camps,Camp_ID',
            'Parent_FirstName' => 'required|string|max:50',
            'Parent_LastName' => 'required|string|max:50',
            'Camper_FirstName' => 'required|string|max:50',
            'Camper_LastName' => 'required|string|max:50',
            'Gender' => 'required|string',
            'Birth_Date' => 'required|date',
            'Address' => 'required|string|max:255',
            'City' => 'required|string|max:100',
            'State' => 'required|string|max:50',
            'Postal_Code' => 'required|string|max:10',
            'Email' => 'required|email|max:255',
            'Phone' => 'required|string|max:20',
            'Shirt_Size' => 'required|string',
            'Allergies' => 'nullable|string',
            'Asthma' => 'required|boolean',
            'medication_status_choice' => 'required|boolean',
            'Medication_Status' => 'nullable|string',
            'Injuries' => 'nullable|string',
            'Church_Name' => 'nullable|string|max:255',
            'Church_Attendance' => 'nullable|string|max:50',
            'teammate_first.*' => 'nullable|string|max:50',
            'teammate_last.*' => 'nullable|string|max:50',
            'promo_code' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            $phoneNumber = preg_replace('/[^0-9]/', '', $validatedData['Phone']);

            Log::info("Starting player registration for {$validatedData['Camper_FirstName']} {$validatedData['Camper_LastName']}");
            Log::info("Parent email: {$validatedData['Email']}, phone: {$phoneNumber}");

            // First create or find the parent
            $parent = ParentModel::firstOrCreate(
                [
                    'Email' => $validatedData['Email'],
                    'Phone' => $phoneNumber
                ],
                [
                    'Parent_FirstName' => $validatedData['Parent_FirstName'],
                    'Parent_LastName' => $validatedData['Parent_LastName'],
                    'Address' => $validatedData['Address'],
                    'City' => $validatedData['City'],
                    'State' => $validatedData['State'],
                    'Postal_Code' => $validatedData['Postal_Code'],
                    'Church_Name' => $validatedData['Church_Name'],
                    'Church_Attendance' => $validatedData['Church_Attendance']
                ]
            );

            Log::info("Parent found/created with Parent_ID: {$parent->Parent_ID}");

            // Then create the player record
            Log::info("Creating player for Parent_ID: {$parent->Parent_ID}");

            $playerId = DB::table('Players')->insertGetId([
                'Parent_ID' => $parent->Parent_ID,
                //'Division_Name' => $validatedData['Division_Name'],
                'Camper_FirstName' => $validatedData['Camper_FirstName'],
                'Camper_LastName' => $validatedData['Camper_LastName'],
                'Gender' => $validatedData['Gender'],
                'Birth_Date' => $validatedData['Birth_Date'],
                'Shirt_Size' => $validatedData['Shirt_Size'],
                'Allergies' => $validatedData['Allergies'],
                'Asthma' => $validatedData['Asthma'],
                'Medication_Status' => $validatedData['Medication_Status'],
                'Injuries' => $validatedData['Injuries']
            ]);

            Log::info("Player created successfully with Player_ID: {$playerId}");

            // Create the relationship between player and camp in Player_Camp table
            DB::table('Player_Camp')->insert([
                'Player_ID' => $playerId,
                'Camp_ID' => $validatedData['Camp_ID']
            ]);

            Log::info("Player-Camp relationship created: Player_ID {$playerId} -> Camp_ID {$validatedData['Camp_ID']}");

            // Handle teammate requests (if any)
            $firstNames = $request->input('teammate_first', []);
            $lastNames = $request->input('teammate_last', []);

            // Validate there are the same amount of last and first names
            if (count($firstNames) !== count($lastNames)) {
                return redirect()->back()->withInput()->with('error', 'Invalid teammate request data.');
            }

            $requestsToInsert = [];
            foreach ($firstNames as $i => $first) {
                $first = trim($first);
                $last = trim($lastNames[$i]);

                if ($first === null && $last === null) {
                    continue; // skip empty rows
                }

                $requestsToInsert[] = [
                    'Player_ID' => $playerId,
                    'Camp_ID' => $validatedData['Camp_ID'],
                    'Requested_FirstName' => $first,
                    'Requested_LastName' => $last,
                ];
            }

            if (!empty($requestsToInsert)) {
                DB::table('Teammate_Request')->insert($requestsToInsert);
            }

            // Redirect to payment page with add-ons
            $addOnsString = $request->input('selected_add_ons', '');
            return redirect()->route('payment.show', [
                'player' => $playerId,
                'camp' => $validatedData['Camp_ID'],
                'discountAmount' => $validatedData['discount_amount'] ?? 0,
                'addOns' => $addOnsString
            ])->with('success', 'Registration completed! Please proceed with payment.')
              ->with('discount_amount', $validatedData['discount_amount'] ?? null)
              ->with('selected_add_ons', $addOnsString);
        } catch (\Exception $e) {
            Log::error("Exception in PlayerController store method: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Registration failed: ');
        }
    }

    /**
     * Update player information via AJAX from dashboard
     */
    public function updateAjax(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'player_id' => 'required|integer',
                'Camper_FirstName' => 'required|string|max:50',
                'Camper_LastName' => 'required|string|max:50',
                'Birth_Date' => 'required|date',
                'Gender' => 'required|string|in:M,F',
                'Shirt_Size' => 'nullable|string',
                'Medications' => 'nullable|string',
                'Allergies' => 'nullable|string',
                'Injuries' => 'nullable|string',
                'Asthma' => 'required|boolean',
            ]);

            // Verify the player belongs to the logged-in parent
            $player = Player::where('Player_ID', $request->player_id)
                ->where('Parent_ID', Auth::user()->parent->Parent_ID)
                ->firstOrFail();

            // Update player data
            $player->update([
                'Camper_FirstName' => $request->Camper_FirstName,
                'Camper_LastName' => $request->Camper_LastName,
                'Birth_Date' => $request->Birth_Date,
                'Gender' => $request->Gender,
                'Shirt_Size' => $request->Shirt_Size,
                'Medications' => $request->Medications ?: 'None',
                'Allergies' => $request->Allergies ?: 'None',
                'Injuries' => $request->Injuries ?: 'None',
                'Asthma' => $request->Asthma,
            ]);

            Log::info("Player updated successfully: Player_ID {$player->Player_ID}");

            return response()->json([
                'success' => true,
                'player' => $player->fresh(),
                'message' => 'Player information updated successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found or you do not have permission to edit this player.'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error updating player: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the player. Please try again.'
            ], 500);
        }
    }

    /**
     * Soft delete a player by setting Parent_ID to NULL
     */
    public function deleteAjax(Request $request)
    {
        try {
            $request->validate([
                'player_id' => 'required|integer'
            ]);

            // Verify the player belongs to the logged-in parent
            $player = Player::where('Player_ID', $request->player_id)
                ->where('Parent_ID', Auth::user()->parent->Parent_ID)
                ->firstOrFail();

            // Soft delete by setting Parent_ID to NULL
            $player->Parent_ID = null;
            $player->save();

            Log::info("Player soft deleted: Player_ID {$player->Player_ID} by Parent_ID " . Auth::user()->parent->Parent_ID);

            return response()->json([
                'success' => true,
                'message' => 'Player removed from your account successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found or you do not have permission to remove this player.'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting player: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the player. Please try again.'
            ], 500);
        }
    }

    /**
     * Add a new player via AJAX from dashboard
     */
    public function addAjax(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'Camper_FirstName' => 'required|string|max:50',
                'Camper_LastName' => 'required|string|max:50',
                'Birth_Date' => 'required|date',
                'Gender' => 'required|string|in:M,F',
                'Shirt_Size' => 'required|string',
                'Medications' => 'nullable|string',
                'Allergies' => 'nullable|string',
                'Injuries' => 'nullable|string',
                'Asthma' => 'required|boolean',
            ]);

            // Get the parent ID from the authenticated user
            $parentId = Auth::user()->parent->Parent_ID;

            // Create the new player
            $player = Player::create([
                'Parent_ID' => $parentId,
                'Camper_FirstName' => $request->Camper_FirstName,
                'Camper_LastName' => $request->Camper_LastName,
                'Birth_Date' => $request->Birth_Date,
                'Gender' => $request->Gender,
                'Shirt_Size' => $request->Shirt_Size,
                'Medications' => $request->Medications ?: 'None',
                'Allergies' => $request->Allergies ?: 'None',
                'Injuries' => $request->Injuries ?: 'None',
                'Asthma' => $request->Asthma,
            ]);

            Log::info("New player added: Player_ID {$player->Player_ID} for Parent_ID {$parentId}");

            return response()->json([
                'success' => true,
                'player' => $player,
                'message' => 'Player added successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error adding player: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the player. Please try again.'
            ], 500);
        }
    }

    /**
     * Get add-ons (extra fees) for a camp
     */
    public function getAddOns($campId)
    {
        try {
            $camp = Camp::find($campId);
            if (!$camp) {
                return response()->json(['add_ons' => []], 404);
            }

            $addOns = ExtraFee::where('Camp_ID', $campId)->get();
            
            return response()->json([
                'add_ons' => $addOns
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching add-ons: ' . $e->getMessage());
            return response()->json(['add_ons' => []], 500);
        }
    }
}
