<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ParentModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update user and parent information via AJAX from dashboard.
     */
    public function updateAjax(Request $request)
    {
        try {
            $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'Phone' => 'nullable|string|max:20',
                'Address' => 'nullable|string|max:255',
                'City' => 'nullable|string|max:100',
                'State' => 'nullable|string|max:2',
                'Postal_Code' => 'nullable|string|max:10',
                'Church_Name' => 'nullable|string|max:255',
            ]);

            // Update user's fname and lname
            $user = Auth::user();
            $user->fname = $request->fname;
            $user->lname = $request->lname;
            $user->save();

            // Only process parent data if the parent record already exists
            $parentData = null;
            if ($request->has(['Phone', 'Address', 'City'])) {
                $parent = ParentModel::where('Email', $user->email)->first();

                if ($parent) {
                    $parent->update([
                        'Phone' => $request->Phone,
                        'Address' => $request->Address,
                        'City' => $request->City,
                        'State' => $request->State,
                        'Postal_Code' => $request->Postal_Code,
                        'Church_Name' => $request->Church_Name,
                    ]);
                    $parentData = $parent;
                }
            }

            return response()->json([
                'success' => true,
                'user' => $user->fresh(),
                'parent' => $parentData
            ]);
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
