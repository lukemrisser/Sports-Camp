<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ParentModel;
use App\Models\Camp;

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
            'teammate_last.*' => 'nullable|string|max:50'
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
            
            // Redirect to payment page instead of back to registration
            return redirect()->route('payment.show', [
                'player' => $playerId, 
                'camp' => $validatedData['Camp_ID']
            ])->with('success', 'Registration completed! Please proceed with payment.');
        } catch (\Exception $e) {
            Log::error("Exception in PlayerController store method: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
}
