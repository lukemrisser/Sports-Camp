<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\Camp;

class HomeController extends Controller
{
    public function index()
    {
        // Get all sports
        $sports = Sport::all();

        //Use images eventually
        $sportMetadata = [
            'soccer' => ['icon' => '⚽'],
            'volleyball' => ['icon' => '🏐'],
            'tennis' => ['icon' => '🎾'],
            'basketball' => ['icon' => '🏀'],
            'baseball' => ['icon' => '⚾'],
            'football' => ['icon' => '🏈'],
        ];


        $registrationCards = $sports->map(function ($sport) use ($sportMetadata) {
            $sportName = strtolower($sport->Sport_Name);
            $metadata = ['icon' => '⭐️'];

            // Match sport name to get appropriate icon and color
            foreach ($sportMetadata as $keyword => $data) {
                if (str_contains($sportName, $keyword)) {
                    $metadata = $data;
                    break;
                }
            }

            return [
                'id' => $sport->Sport_ID,
                'title' => $sport->Sport_Name.' Camp',
                'icon' => $metadata['icon'],
                'route' => 'sport.show',
                'color' => 'blue',
            ];
        })->toArray();

        return view('home', compact('registrationCards'));
    }
}
