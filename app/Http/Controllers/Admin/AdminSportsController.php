<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\FAQ;
use App\Models\Sponsor;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminSportsController extends Controller
{
    public function index()
    {
        $sports = Sport::with(['faqs', 'sponsors', 'galleryImages'])->orderBy('Sport_Name')->get();
        return view('admin.manage-sports', compact('sports'));
    }

    public function show($id)
    {
        $sport = Sport::with(['faqs', 'sponsors', 'galleryImages'])->findOrFail($id);
        return response()->json($sport);
    }

    public function store(Request $request)
    {
        // Check for upload errors
        if ($request->hasFile('sponsors')) {
            foreach ($request->file('sponsors') as $index => $sponsor) {
                if (isset($sponsor['image']) && $sponsor['image']->getError() !== UPLOAD_ERR_OK) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'File size exceeds PHP upload_max_filesize limit.',
                        UPLOAD_ERR_FORM_SIZE => 'File size exceeds form MAX_FILE_SIZE limit.',
                        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload directory.',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
                    ];
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Sponsor image upload error: ' . ($errorMessages[$sponsor['image']->getError()] ?? 'Unknown error'));
                }
            }
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $galleryImage) {
                if (isset($galleryImage['image']) && $galleryImage['image']->getError() !== UPLOAD_ERR_OK) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'File size exceeds PHP upload_max_filesize limit.',
                        UPLOAD_ERR_FORM_SIZE => 'File size exceeds form MAX_FILE_SIZE limit.',
                        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload directory.',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
                    ];
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gallery image upload error: ' . ($errorMessages[$galleryImage['image']->getError()] ?? 'Unknown error'));
                }
            }
        }

        $validated = $request->validate([
            'sport_name' => 'required|string|max:100|unique:Sports,Sport_Name',
            'sport_description' => 'nullable|string|max:1000',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required_with:faqs.*|string|max:500',
            'faqs.*.answer' => 'required_with:faqs.*|string|max:2000',
            'sponsors' => 'nullable|array',
            'sponsors.*.name' => 'required_with:sponsors.*|string|max:100',
            'sponsors.*.link' => 'nullable|url|max:255',
            'sponsors.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'gallery_images' => 'nullable|array',
            'gallery_images.*.title' => 'required_with:gallery_images.*|string|max:255',
            'gallery_images.*.text' => 'nullable|string|max:1000',
            'gallery_images.*.image' => 'required_with:gallery_images.*|image|mimes:jpeg,png,jpg,gif,svg|max:15360',
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

            // Add gallery images if provided
            if (!empty($validated['gallery_images'])) {
                foreach ($request->gallery_images as $index => $galleryImage) {
                    if (!empty($galleryImage['title']) && $request->hasFile("gallery_images.{$index}.image")) {
                        $imagePath = $request->file("gallery_images.{$index}.image")
                            ->store('gallery-images', 'public');
                        
                        $sport->galleryImages()->create([
                            'Image_Title' => $galleryImage['title'],
                            'Image_Text' => $galleryImage['text'] ?? null,
                            'Image_path' => $imagePath,
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
            'sponsors.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'sponsors.*.current_image' => 'nullable|string|max:255',
            'gallery_images' => 'nullable|array',
            'gallery_images.*.title' => 'required_with:gallery_images.*|string|max:255',
            'gallery_images.*.text' => 'nullable|string|max:1000',
            'gallery_images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:15360',
            'gallery_images.*.current_image' => 'nullable|string|max:255',
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

            // Update gallery images - remove existing and add new ones
            // First, get old gallery image paths to delete them later
            $oldGalleryImages = $sport->galleryImages()->whereNotNull('Image_path')->pluck('Image_path')->toArray();
            $sport->galleryImages()->delete();
            
            if (!empty($validated['gallery_images'])) {
                foreach ($request->gallery_images as $index => $galleryImage) {
                    if (!empty($galleryImage['title'])) {
                        $imagePath = null;
                        
                        // Handle image upload
                        if ($request->hasFile("gallery_images.{$index}.image")) {
                            $imagePath = $request->file("gallery_images.{$index}.image")
                                ->store('gallery-images', 'public');
                        } elseif (!empty($galleryImage['current_image'])) {
                            // Keep existing image if no new one uploaded
                            $imagePath = $galleryImage['current_image'];
                            // Remove from deletion list
                            $oldGalleryImages = array_diff($oldGalleryImages, [$imagePath]);
                        }
                        
                        // Only create if we have an image (either new or existing)
                        if ($imagePath) {
                            $sport->galleryImages()->create([
                                'Image_Title' => $galleryImage['title'],
                                'Image_Text' => $galleryImage['text'] ?? null,
                                'Image_path' => $imagePath,
                            ]);
                        }
                    }
                }
            }
            
            // Delete unused old gallery images
            foreach ($oldGalleryImages as $oldGalleryImage) {
                Storage::disk('public')->delete($oldGalleryImage);
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
            
            // Delete gallery images
            $galleryImages = $sport->galleryImages()->whereNotNull('Image_path')->pluck('Image_path')->toArray();
            foreach ($galleryImages as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            
            // Delete related FAQs, sponsors, and gallery images
            $sport->faqs()->delete();
            $sport->sponsors()->delete();
            $sport->galleryImages()->delete();
            
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