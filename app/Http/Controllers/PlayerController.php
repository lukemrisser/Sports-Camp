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
            'Church_Attendance' => 'required|string'
        ]);

        try {
            $phoneNumber = preg_replace('/[^0-9]/', '', $validatedData['Phone']);
            
            DB::table('Players')->insert([
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

            return redirect()->back()->with('success', 'Registration submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while submitting the registration. Please try again.');
        }
    }
}