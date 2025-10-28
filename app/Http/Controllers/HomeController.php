<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camp;

class HomeController extends Controller
{
    public function index()
    {
       $camps = Camp::getAvailableForRegistration();

        $campMetadata = [
            'soccer' => ['icon' => '⚽', 'color' => 'blue'],
            'volleyball' => ['icon' => '🏐', 'color' => 'green'],
            'tennis' => ['icon' => '🎾', 'color' => 'purple'],
        ];

        $registrationCards = $camps->map(function ($camp) use ($campMetadata) {
            
            $title = strtolower($camp->Camp_Name);
            $metadata = ['icon' => '⭐️', 'color' => 'orange'];

            foreach ($campMetadata as $keyword => $data) {
                if (str_contains($title, $keyword)) {
                    $metadata = $data;
                    break;
                }
            }
            
            if ($camp->Camp_Gender == 'boys')
                $gender = 'Boys ';
            else if ($camp->Camp_Gender == 'girls')
                $gender = 'Girls ';
            else
                $gender = 'Coed ';

            $ageRange = ": Ages {$camp->Age_Min}-{$camp->Age_Max}";
            $fullTitle = $gender . $camp->Camp_Name . $ageRange;

            return [
                'id' => $camp->Camp_ID,
                'title' => $fullTitle,
                'description' => $camp->Description,
                'icon' => $metadata['icon'],
                'route' => 'registration.form', 
                'color' => $metadata['color']
            ];
        })->toArray();

        return view('home', compact('registrationCards'));
    }
}
