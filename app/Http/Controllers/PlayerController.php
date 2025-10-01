<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Division_Name' => 'required|string|max:50',
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
            'Age' => 'required|integer',
            'Shirt_Size' => 'required|string',
            'Allergies' => 'nullable|string',
            'Asthma' => 'required|boolean',
            'Medication_Status' => 'required|boolean',
            'Injuries' => 'nullable|string',
            'Church_Name' => 'nullable|string|max:255',
            'Church_Attendance' => 'required|string',
            'teammate_first.*' => 'nullable|string|max:50',
            'teammate_last.*' => 'nullable|string|max:50'
        ]);

        try {
            $phoneNumber = preg_replace('/[^0-9]/', '', $validatedData['Phone']);
            
            $playerId = DB::table('Players')->insertGetId([
                'Division_Name' => $validatedData['Division_Name'],
                'Parent_FirstName' => $validatedData['Parent_FirstName'],
                'Parent_LastName' => $validatedData['Parent_LastName'],
                'Camper_FirstName' => $validatedData['Camper_FirstName'],
                'Camper_LastName' => $validatedData['Camper_LastName'],
                'Gender' => $validatedData['Gender'],
                'Birth_Date' => $validatedData['Birth_Date'],
                'Address' => $validatedData['Address'],
                'City' => $validatedData['City'],
                'State' => $validatedData['State'],
                'Postal_Code' => $validatedData['Postal_Code'],
                'Email' => $validatedData['Email'],
                'Phone' => $phoneNumber,
                'Age' => $validatedData['Age'],
                'Shirt_Size' => $validatedData['Shirt_Size'],
                'Allergies' => $validatedData['Allergies'],
                'Asthma' => $validatedData['Asthma'],
                'Medication_Status' => $validatedData['Medication_Status'],
                'Injuries' => $validatedData['Injuries'],
                'Church_Name' => $validatedData['Church_Name'],
                'Church_Attendance' => $validatedData['Church_Attendance']
            ]);

            // Handle teammate requests (if any)
            $firstNames = $request->input('teammate_first', []);
            $lastNames = $request->input('teammate_last', []);
            
            // Validate there are the same amout of last and first names
            if (count($firstNames) !== count($lastNames)) {
                return redirect()->back()->with('error', 'Invalid teammate request data.');
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
                    'Requested_FirstName' => $first,
                    'Requested_LastName' => $last,
                ];
            }

            if (!empty($requestsToInsert)) {
                DB::table('Teammate_Request')->insert($requestsToInsert);
            }

            return redirect()->back()->with('success', 'Registration submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}