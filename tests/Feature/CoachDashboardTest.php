<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Coach;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CoachDashboardTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Goal: Verify a logged-in coach can successfully access their dashboard
    **/
    
    public function test_coach_dashboard_access(): void {

        // Create a Coach User and verify email
        $coach = User::factory()->create(['email_verified_at' => now()]);

        Coach::factory()->create(['user_id' => $coach->id]);

        // Simulate a Coach being logged into their dashboard
        $response = $this->actingAs($coach)->get(route('coach-dashboard'));

        // Ensure log-in was successful
        $response->assertStatus(200);
    }
    
}
