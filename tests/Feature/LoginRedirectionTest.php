<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Coach;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginRedirectionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Coach Redirection and Parent Redirection
     * Goals: Test a coach logging in and is directed to the correct dashboard; Test a standard user
     * (Parent) logging in 
     */
    public function test_valid_coach_is_redirected_to_coach_dashboard(): void
    {
        // Create a Coach User
        $password = 'testcoach';
        $user = User::factory()->create([
            'email' => 'coach@test.com',
            'password' => Hash::make($password),
            'email_verified_at' => now() // Making sure the email is verified and passes middleware checks
        ]);

        // Link the Coach Model to the User
        Coach::factory()->create(['user_id' => $user->id]);

        // Simulate a Coach Log-in
        $response = $this->post('/login', [
            'email' => 'coach@test.com',
            'password' => $password
        ]);

        // Redirect coach to the coach dashboard
        $response->assertStatus(302);
        $response->assertRedirect(route('coach-dashboard'));

        // Verify the user is now authenticated
        $this->assertAuthenticatedAs($user);
    }

    public function test_parent_is_redirected_to_default_dashboard(): void {
        
        // Create a Parent User
        $password = 'testparent';
        $user = User::factory()->create([
            'email' => 'parent@test.com',
            'password' => Hash::make($password),
            'email_verified_at' => now()
        ]);

        // Ensure the user is NOT a Coach
        $this->assertFalse($user->isCoach());

        // Simulate a Parent Logging in
        $response = $this->post('/login', [
            'email' => 'parent@test.com',
            'password' => $password
        ]);

        // Redirect parent to default dashboard
        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));

        // Ensure the Parent is authenticated
        $this->assertAuthenticatedAs($user);
    }
}
