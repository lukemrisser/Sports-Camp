<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSportsController extends Controller
{
    public function index()
    {
        $sports = Sport::orderBy('Sport_Name')->get();
        return view('admin.manage-sports', compact('sports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sport_name' => 'required|string|max:100|unique:Sports,Sport_Name',
        ]);

        Sport::create([
            'Sport_Name' => $validated['sport_name'],
        ]);

        return redirect()->route('admin.manage-sports')
            ->with('success', 'Sport added successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sport_name' => 'required|string|max:100|unique:Sports,Sport_Name,' . $id . ',Sport_ID',
        ]);

        $sport = Sport::findOrFail($id);
        $sport->update([
            'Sport_Name' => $validated['sport_name'],
        ]);

        return redirect()->route('admin.manage-sports')
            ->with('success', 'Sport updated successfully!');
    }

    public function destroy($id)
    {
        $sport = Sport::findOrFail($id);
        
        // Check if sport is being used by any camps or coaches
        $campsCount = $sport->camps()->count();
        $coachesCount = $sport->coaches()->count();
        
        if ($campsCount > 0 || $coachesCount > 0) {
            return redirect()->route('admin.manage-sports')
                ->with('error', "Cannot delete sport '{$sport->Sport_Name}' because it is assigned to {$campsCount} camps and {$coachesCount} coaches.");
        }

        $sport->delete();

        return redirect()->route('admin.manage-sports')
            ->with('success', 'Sport deleted successfully!');
    }
}