<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'Phone' => 'nullable|string|max:20',
            'Address' => 'nullable|string|max:255',
            'City' => 'nullable|string|max:100',
            'State' => 'nullable|string|max:2',
            'Postal_Code' => 'nullable|string|max:10',
            'Church_Name' => 'nullable|string|max:255',
        ]);

        // Get the user's name parts
        $nameParts = explode(' ', Auth::user()->name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Create parent record with user's email
        ParentModel::create([
            'Parent_FirstName' => $firstName,
            'Parent_LastName' => $lastName,
            'Email' => Auth::user()->email,
            'Phone' => $request->Phone,
            'Address' => $request->Address,
            'City' => $request->City,
            'State' => $request->State,
            'Postal_Code' => $request->Postal_Code,
            'Church_Name' => $request->Church_Name,
            'Church_Attendance' => $request->Church_Attendance ?? null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Contact information saved successfully!');
    }
}
