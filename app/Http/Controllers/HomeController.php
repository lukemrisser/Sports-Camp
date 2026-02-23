<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        // Get all sports
        $sports = Sport::all();

        $registrationCards = $sports->map(function ($sport) {
            $sportImageUrl = $this->imageUrl($sport->Sport_Image);

            return [
                'id' => $sport->Sport_ID,
                'title' => $sport->Sport_Name.' Camp',
                'icon' => '⭐️',
                'image_url' => $sportImageUrl,
                'route' => 'sport.show',
                'color' => 'blue',
            ];
        })->toArray();

        return view('home', compact('registrationCards'));
    }

    private function imageUrl(?string $imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('cloudinary');

        return $disk->url($imagePath);
    }

    public function help()
    {
        return view('help');
    }
}
