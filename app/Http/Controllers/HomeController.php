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
                'title' => 'Youth Sports Camp',
                'description' => 'Register for our youth sports training program (Ages 8-16)',
                'icon' => '⚽',
                'route' => 'youth-sports-registration',
                'color' => 'blue'
            ],
            [
                'title' => 'Adult Fitness Program',
                'description' => 'Join our adult fitness and conditioning classes',
                'icon' => '🏋️',
                'route' => 'adult-fitness-registration',
                'color' => 'green'
            ],
            [
                'title' => 'Team Registration',
                'description' => 'Register your entire team for league play',
                'icon' => '👥',
                'route' => 'team-registration',
                'color' => 'purple'
            ],
            [
                'title' => 'Coach Application',
                'description' => 'Apply to become a coach or volunteer',
                'icon' => '📋',
                'route' => 'coach-application',
                'color' => 'orange'
            ]
        ];

        return view('home', compact('registrationCards'));
    }
}
