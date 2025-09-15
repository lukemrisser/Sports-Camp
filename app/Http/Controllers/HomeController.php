<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the registration dashboard with clickable cards
     */
    public function index()
    {
        // Define the registration options/cards
        $registrationCards = [
            [
                'title' => 'Youth Soccer',
                'description' => 'Register for our youth soccer camp',
                'icon' => '⚽',
                'route' => 'registration',
                'color' => 'blue'
            ],
            [
                'title' => 'Youth Volleyball',
                'description' => 'Join our youth volleyball camp',
                'icon' => '🏐',
                'route' => 'registration',
                'color' => 'green'
            ],
            [
                'title' => 'Youth Tennis',
                'description' => 'Register your child for tennis camp',
                'icon' => '🎾',
                'route' => 'registration',
                'color' => 'purple'
            ],

        ];

        return view('home', compact('registrationCards'));
    }
}
