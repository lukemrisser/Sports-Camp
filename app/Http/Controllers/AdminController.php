<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Coach;
use App\Models\Camp;
use App\Models\Player;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function finances()
    {
        return view('admin.finances');
    }

    public function inviteCoach()
    {
        return view('admin.invite-coach');
    }

    public function manageCoaches()
    {
        return view('admin.manage-coaches');
    }
}
