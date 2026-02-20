<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Camp;
use App\Models\Sport;
use App\Models\Coach;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminFinancesTest extends TestCase
{
    use RefreshDatabase; // Uses the Laravel in-memory database, not using real database data

    protected $admin;
    
    // Protected function to set up sport
    protected function setUp(): void {
        parent::setUp();

        // 1. Create the Sport, allows us to link coaches/camps to it
        $this->sport = Sport::create([
            'Sport_Name' => 'Basketball'
        ]);
    }

    /** Ensures Coaches (for now) can access the Finances Page */
    public function test_coach_can_access_adminFinancesPage() {
        // Create a coach
        $user = User::factory()->create();

        Coach::create([
            'user_id' => $user->id,
            'Coach_Firstname' => $user->fname,
            'Coach_Lastname' => $user->lname,
            'Sport_ID' => $this->sport->Sport_ID,
            'admin' => true, // Standard coach, non-admin
        ]);

        // Access the adminFinancesPage
        $response = $this->actingAs($user)->get(route('admin.finances'));

        // Access successful
        $response->assertStatus(200);
    }

    /** Ensure Admin can access Admin Finances Page */
    public function test_admin_can_access_adminFinances() {
        // Create an admin user
        $adminUser = User::factory()->create();

        Coach::create([
            'user_id' => $adminUser->id,
            'Sport_ID' => $this->sport->Sport_ID,
            'admin' => true // Admin coach
        ]);

        // Access the Admin Finances Page
        $response = $this->actingAs($adminUser)->get(route('admin.finances'));

        $response->assertStatus(200);
    }
    
    /** Ensure unauthorized users cannot access the Admin Finances Page */
    public function test_non_coach_or_admin_can_access_adminFinancesPage() {
        // Create a user (non-coach, non-admin)
        $user = User::factory()->create();

        // Attempt to Access Admin Finances Page
        $response = $this->actingAs($user)->get(route('admin.finances'));

        // Gets redirected since they are unauthorized to access the page
        $response->assertStatus(403);
    }

    /** Ensure export and download functions for the Admin Finances Page work */
    public function test_export_and_download_successful() {
        // Create an authorized user
        $user = User::factory()->create();

        Coach::create([
            'user_id' => $user->id,
            'Sport_ID' => $this->sport->Sport_ID,
            'admin' => true,
        ]);

        // Export file containing finances through 2025
        $response = $this->actingAs($user)->post(route('admin.finances.export'), [
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }
}