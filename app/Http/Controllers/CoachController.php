<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

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
}
