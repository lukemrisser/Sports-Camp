<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\FAQ;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminSportsController extends Controller
{
    public function index()
    {
        $sports = Sport::with(['faqs', 'sponsors'])->orderBy('Sport_Name')->get();
        return view('admin.manage-sports', compact('sports'));
    }

    public function show($id)
    {
        $sport = Sport::with(['faqs', 'sponsors'])->findOrFail($id);
        return response()->json($sport);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sport_name' => 'required|string|max:100|unique:Sports,Sport_Name',
            'sport_description' => 'nullable|string|max:1000',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required_with:faqs.*|string|max:500',
            'faqs.*.answer' => 'required_with:faqs.*|string|max:2000',
            'sponsors' => 'nullable|array',
            'sponsors.*.name' => 'required_with:sponsors.*|string|max:100',
            'sponsors.*.link' => 'nullable|url|max:255',
            'sponsors.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $sport = Sport::create([
                'Sport_Name' => $validated['sport_name'],
                'Sport_Description' => $validated['sport_description'] ?? null,
            ]);

            // Add FAQs if provided
            if (!empty($validated['faqs'])) {
                foreach ($validated['faqs'] as $faq) {
                    if (!empty($faq['question']) && !empty($faq['answer'])) {
                        $sport->faqs()->create([
                            'Question' => $faq['question'],
                            'Answer' => $faq['answer'],
                        ]);
                    }
                }
            }

            // Add sponsors if provided
            if (!empty($validated['sponsors'])) {
                foreach ($request->sponsors as $index => $sponsor) {
                    if (!empty($sponsor['name'])) {
                        $imagePath = null;
                        
                        // Handle image upload
                        if ($request->hasFile("sponsors.{$index}.image")) {
                            $imagePath = $request->file("sponsors.{$index}.image")
                                ->store('sponsor-logos', 'public');
                        }
                        
                        $sport->sponsors()->create([
                            'Sponsor_Name' => $sponsor['name'],
                            'Sponsor_Link' => $sponsor['link'] ?? null,
                            'Image_Path' => $imagePath,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.manage-sports')
                ->with('success', 'Sport added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating sport: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sport_name' => 'required|string|max:100|unique:Sports,Sport_Name,' . $id . ',Sport_ID',
            'sport_description' => 'nullable|string|max:1000',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required_with:faqs.*|string|max:500',
            'faqs.*.answer' => 'required_with:faqs.*|string|max:2000',
            'sponsors' => 'nullable|array',
            'sponsors.*.name' => 'required_with:sponsors.*|string|max:100',
            'sponsors.*.link' => 'nullable|url|max:255',
            'sponsors.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sponsors.*.current_image' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $sport = Sport::findOrFail($id);
            $sport->update([
                'Sport_Name' => $validated['sport_name'],
                'Sport_Description' => $validated['sport_description'] ?? null,
            ]);

            // Update FAQs - remove existing and add new ones
            $sport->faqs()->delete();
            if (!empty($validated['faqs'])) {
                foreach ($validated['faqs'] as $faq) {
                    if (!empty($faq['question']) && !empty($faq['answer'])) {
                        $sport->faqs()->create([
                            'Question' => $faq['question'],
                            'Answer' => $faq['answer'],
                        ]);
                    }
                }
            }

            // Update sponsors - remove existing and add new ones
            // First, get old image paths to delete them later
            $oldImages = $sport->sponsors()->whereNotNull('Image_Path')->pluck('Image_Path')->toArray();
            $sport->sponsors()->delete();
            
            if (!empty($validated['sponsors'])) {
                foreach ($request->sponsors as $index => $sponsor) {
                    if (!empty($sponsor['name'])) {
                        $imagePath = null;
                        
                        // Handle image upload
                        if ($request->hasFile("sponsors.{$index}.image")) {
                            $imagePath = $request->file("sponsors.{$index}.image")
                                ->store('sponsor-logos', 'public');
                        } elseif (!empty($sponsor['current_image'])) {
                            // Keep existing image if no new one uploaded
                            $imagePath = $sponsor['current_image'];
                            // Remove from deletion list
                            $oldImages = array_diff($oldImages, [$imagePath]);
                        }
                        
                        $sport->sponsors()->create([
                            'Sponsor_Name' => $sponsor['name'],
                            'Sponsor_Link' => $sponsor['link'] ?? null,
                            'Image_Path' => $imagePath,
                        ]);
                    }
                }
            }
            
            // Delete unused old images
            foreach ($oldImages as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            DB::commit();
            return redirect()->route('admin.manage-sports')
                ->with('success', 'Sport updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating sport: ' . $e->getMessage());
        }
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

        DB::beginTransaction();
        try {
            // Delete sponsor images first
            $sponsorImages = $sport->sponsors()->whereNotNull('Image_Path')->pluck('Image_Path')->toArray();
            foreach ($sponsorImages as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            
            // Delete related FAQs and sponsors
            $sport->faqs()->delete();
            $sport->sponsors()->delete();
            
            // Then delete the sport
            $sport->delete();
            
            DB::commit();
            return redirect()->route('admin.manage-sports')
                ->with('success', 'Sport deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.manage-sports')
                ->with('error', 'Error deleting sport: ' . $e->getMessage());
        }
    }
}