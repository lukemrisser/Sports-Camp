<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class CoachController extends Controller
{   

    public function uploadSpreadsheet(Request $request)
    {
        $request->validate([
            'spreadsheet' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('spreadsheet');
        // Parse the file using Excel::toArray() or similar
        // Your logic here

        return back()->with('success', 'Spreadsheet uploaded!');
    }
}