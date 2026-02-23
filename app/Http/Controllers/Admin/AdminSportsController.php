<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\FAQ;
use App\Models\Sponsor;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSportsController extends Controller
{
    private const IMAGE_DISK = 'cloudinary';
    private const MAX_SPONSOR_IMAGE_KB = 20480;
    private const MAX_GALLERY_IMAGE_KB = 30720;
    private const MAX_UPLOAD_INPUT_KB = 51200;
    private const MIN_OPTIMIZE_QUALITY = 40;
    private const ALWAYS_NORMALIZE_MIMES = [
        'image/jpeg',
        'image/jpg',
        'image/pjpeg',
        'image/webp',
    ];

    public function index()
    {
        $sports = Sport::with(['faqs', 'sponsors', 'galleryImages'])->orderBy('Sport_Name')->get();
        return view('admin.manage-sports', compact('sports'));
    }

    public function show($id)
    {
        $sport = Sport::with(['faqs', 'sponsors', 'galleryImages'])->findOrFail($id);

        $sport->sponsors->transform(function ($sponsor) {
            $sponsor->image_url = $this->imageUrl($sponsor->Image_Path);
            return $sponsor;
        });

        $sport->galleryImages->transform(function ($galleryImage) {
            $galleryImage->image_url = $this->imageUrl($galleryImage->Image_path);
            return $galleryImage;
        });

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
            'sponsors.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:' . self::MAX_UPLOAD_INPUT_KB,
            'gallery_images' => 'nullable|array',
            'gallery_images.*.title' => 'required_with:gallery_images.*|string|max:255',
            'gallery_images.*.text' => 'nullable|string|max:1000',
            'gallery_images.*.image' => 'required_with:gallery_images.*|image|mimes:jpeg,png,jpg,gif,svg,webp|max:' . self::MAX_UPLOAD_INPUT_KB,
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
                            $imagePath = $this->storeOptimizedImage(
                                $request->file("sponsors.{$index}.image"),
                                'sponsor-logos',
                                self::MAX_SPONSOR_IMAGE_KB
                            );
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
                        $imagePath = $this->storeOptimizedImage(
                            $request->file("gallery_images.{$index}.image"),
                            'gallery-images',
                            self::MAX_GALLERY_IMAGE_KB
                        );
                        
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
            'sponsors.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:' . self::MAX_UPLOAD_INPUT_KB,
            'sponsors.*.current_image' => 'nullable|string|max:255',
            'gallery_images' => 'nullable|array',
            'gallery_images.*.title' => 'required_with:gallery_images.*|string|max:255',
            'gallery_images.*.text' => 'nullable|string|max:1000',
            'gallery_images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:' . self::MAX_UPLOAD_INPUT_KB,
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
                            $imagePath = $this->storeOptimizedImage(
                                $request->file("sponsors.{$index}.image"),
                                'sponsor-logos',
                                self::MAX_SPONSOR_IMAGE_KB
                            );
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
                $this->deleteImage($oldImage);
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
                            $imagePath = $this->storeOptimizedImage(
                                $request->file("gallery_images.{$index}.image"),
                                'gallery-images',
                                self::MAX_GALLERY_IMAGE_KB
                            );
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
                $this->deleteImage($oldGalleryImage);
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
                $this->deleteImage($imagePath);
            }
            
            // Delete gallery images
            $galleryImages = $sport->galleryImages()->whereNotNull('Image_path')->pluck('Image_path')->toArray();
            foreach ($galleryImages as $imagePath) {
                $this->deleteImage($imagePath);
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

    private function imageUrl(?string $imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(self::IMAGE_DISK);

        return $disk->url($imagePath);
    }

    private function deleteImage(?string $imagePath): void
    {
        if (empty($imagePath)) {
            return;
        }

        try {
            Storage::disk(self::IMAGE_DISK)->delete($imagePath);
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'Resource not found')) {
                Log::info('Cloudinary image already missing during delete; skipping.', [
                    'image_path' => $imagePath,
                    'message' => $e->getMessage(),
                ]);
                return;
            }

            if (!app()->environment('production') && str_contains($e->getMessage(), 'cURL error 60')) {
                Log::warning('Cloudinary delete skipped in local due SSL certificate trust issue.', [
                    'image_path' => $imagePath,
                    'message' => $e->getMessage(),
                ]);
                return;
            }

            throw $e;
        }
    }

    private function storeOptimizedImage(UploadedFile $file, string $directory, int $targetMaxKb): string
    {
        $targetBytes = $targetMaxKb * 1024;
        $currentSize = $file->getSize() ?: 0;
        $mime = strtolower((string) $file->getMimeType());
        $shouldNormalize = $currentSize > $targetBytes || in_array($mime, self::ALWAYS_NORMALIZE_MIMES, true);

        if ($shouldNormalize) {
            $optimizedImage = $this->optimizeImageToTarget($file, $targetBytes);

            if ($optimizedImage !== null) {
                [$optimizedBytes, $extension] = $optimizedImage;
                $path = $directory . '/' . Str::uuid() . '.' . $extension;
                Storage::disk(self::IMAGE_DISK)->put($path, $optimizedBytes);

                return $path;
            }

            if ($currentSize > $targetBytes) {
                $maxMb = (int) round($targetMaxKb / 1024);
                throw new \RuntimeException("Image is too large and could not be optimized below {$maxMb}MB.");
            }
        }

        return $file->store($directory, self::IMAGE_DISK);
    }

    private function optimizeImageToTarget(UploadedFile $file, int $targetBytes): ?array
    {
        $mime = $file->getMimeType();
        $sourceData = @file_get_contents($file->getRealPath());

        if ($sourceData === false) {
            return null;
        }

        $source = @imagecreatefromstring($sourceData);
        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 0 || $height <= 0) {
            return null;
        }

        $scaleSteps = [1.0, 0.9, 0.8, 0.7, 0.6, 0.5];
        $qualitySteps = [90, 82, 75, 68, 60, 52, 45, self::MIN_OPTIMIZE_QUALITY];
        $bestCandidate = null;

        foreach ($scaleSteps as $scale) {
            $targetWidth = max(1, (int) floor($width * $scale));
            $targetHeight = max(1, (int) floor($height * $scale));

            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
            if ($canvas === false) {
                continue;
            }

            if (in_array($mime, ['image/png', 'image/webp'], true)) {
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                imagefill($canvas, 0, 0, $transparent);
            }

            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

            foreach ($qualitySteps as $quality) {
                $encodedResult = $this->encodeImage($canvas, $mime, $quality);
                if ($encodedResult === null) {
                    continue;
                }

                [$encoded, $encodedExtension] = $encodedResult;

                $encodedSize = strlen($encoded);
                if ($encodedSize <= $targetBytes) {
                    return [$encoded, $encodedExtension];
                }

                if ($bestCandidate === null || $encodedSize < strlen($bestCandidate[0])) {
                    $bestCandidate = [$encoded, $encodedExtension];
                }
            }

        }

        if ($bestCandidate !== null && strlen($bestCandidate[0]) <= $targetBytes) {
            return $bestCandidate;
        }

        return null;
    }

    private function encodeImage(\GdImage $image, ?string $mime, int $quality): ?array
    {
        $mimeCandidates = match ($mime) {
            'image/png' => ['image/png'],
            'image/webp' => ['image/webp', 'image/jpeg', 'image/png'],
            'image/jpeg', 'image/jpg' => ['image/jpeg', 'image/webp', 'image/png'],
            default => ['image/jpeg', 'image/png'],
        };

        foreach ($mimeCandidates as $candidateMime) {
            ob_start();

            $success = match ($candidateMime) {
                'image/png' => imagepng($image, null, $this->pngCompressionFromQuality($quality)),
                'image/webp' => function_exists('imagewebp') ? imagewebp($image, null, $quality) : false,
                default => imagejpeg($image, null, $quality),
            };

            if ($success === false) {
                ob_end_clean();
                continue;
            }

            $bytes = ob_get_clean();
            if ($bytes !== false && $bytes !== '') {
                return [$bytes, $this->extensionForMime($candidateMime)];
            }
        }

        return null;
    }

    private function pngCompressionFromQuality(int $quality): int
    {
        $quality = max(self::MIN_OPTIMIZE_QUALITY, min(100, $quality));
        return (int) round((100 - $quality) * 9 / 100);
    }

    private function extensionForMime(?string $mime): string
    {
        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }
}