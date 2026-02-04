<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sport;
use App\Models\Coach;

class CreateCampTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_create_a_camp(): void
    {
        $this->withoutExceptionHandling();

        // Create a coach
        $coach = User::factory()->create(['email_verified_at' => now()]);

        Coach::factory()->create(['user_id' => $coach->id]);

        // Create a sport
        $sport = Sport::factory()->create();

        // User creates a camp
        $response = $this->actingAs($coach)->post('/create-camp', [
            'sport_id'           => $sport->Sport_ID,
            'name'               => 'AROMA Basketball 2026',
            'description'        => 'Youth basketball summer camp',
            'start_date'         => '2026-06-01',
            'end_date'           => '2026-06-06',
            'registration_open'  => '2026-01-01',
            'registration_close' => '2026-05-01',
            'price'              => 150.00,
            'gender'             => 'coed',
            'min_age'            => 8,
            'max_age'            => 12,
            'max_capacity'       => 50,
            'location_name'      => 'Messiah University',
            'street_address'     => 'One University Ave',
            'city'               => 'Mechanicsburg',
            'state'              => 'PA',
            'zip_code'           => '17055',
        ]);


        // Redirect to Created Camp - Confirms Success
        $response->assertStatus(302);


        $this->assertDatabaseHas('camps', ['Camp_Name' => 'AROMA Basketball 2026']);
    }
     
}