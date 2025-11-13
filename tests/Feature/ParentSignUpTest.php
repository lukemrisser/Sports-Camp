<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Player;
use App\Models\Camp;
use Tests\TestCase;

class ParentSignUpTest extends TestCase
{
    // Refresh the database for each run
    use RefreshDatabase;

    // Test out the Camper Registration Workflow
    public function test_camper_registration_creates_all_records_and_redirects_to_payment() {

        // 1. Create a camp and the form data
        $camp = Camp::factory()->create();
        $campId = $camp->Camp_ID;

        $formData = [
            'Camp_ID' => $campId, // Links the player to the camp
            'Parent_FirstName' => 'Dana',
            'Parent_LastName' => 'Falcon',
            'Camper_FirstName' => 'Alex',
            'Camper_LastName' => 'Falcon',
            'Gender' => 'Male',
            'Birth_Date' => '2010-06-15',
            'Address' => '123 Test Street',
            'City' => 'Testville', 
            'State' => 'PA',
            'Postal_Code' => '17055',
            'Email' => 'dana.falcon@example.com',
            'Phone' => '(123) 456-7890',
            'Shirt_Size' => 'Youth Large',
            'Allergies' => 'Peanuts',
            'Asthma' => 1, // true
            'medication_status_choice' => 1,
            'Medication_Status' => 'None',
            'Injuries' => 'None',
            'Church_Name' => 'Eastshore Church',
            'Church_Attendance' => 'Weekly',
            'teammate_first' => ['Chris'],
            'teammate_last' => ['Hemsworth'],
        ];

        // The first Parent registrated are given IDs of 1
        $expectedParentId = 1;

        // 2. Submit the form
        $response = $this->post(route('players.store'), $formData);

        // 3. Verify

        // Assert the Parent record is created
        $this->assertDatabaseHas('Parents', [
            'Parent_ID' => $expectedParentId,
            'Email' => 'dana.falcon@example.com',
            'Phone' => '1234567890'
        ]);

        // Assert the Player record is created
        $this->assertDatabaseHas('Players', [
            'Camper_FirstName' => 'Alex',
            'Parent_ID' => $expectedParentId,
            'Asthma' => 1,
        ]);

        // Dynamically retrieve the Player_ID from the database
        $player = \App\Models\Player::where('Camper_FirstName', 'Alex')
                                    ->where('Camper_LastName', 'Falcon')
                                    ->first();

        // Ensure the player is found
        $this->assertNotNull($player, "Failed to find the newly created player in the database.");

        $actualPlayerId = $player->Player_ID;

        // Check for successfull submission redirection
        $response->assertStatus(302);

        // Check the redirection-target: the payment page
        $response->assertRedirect(route('payment.show', [
            'player' => $actualPlayerId,
            'camp' => $campId
        ]));

        // Check for successful registration message
        $response->assertSessionHas('success', 'Registration completed! Please proceed with payment.');

        // Verify the Player_Camp and Teammate_Request use the actual ID
        $this->assertDatabaseHas('Player_Camp', [
            'Player_ID' => $actualPlayerId,
            'Camp_ID' => $campId,
        ]);

        $this->assertDatabaseHas('Teammate_Request', [
            'Player_ID' => $actualPlayerId,
            'Camp_ID' => $campId,
            'Requested_FirstName' => 'Chris',
            'Requested_LastName' => 'Hemsworth',

        ]);
    }
}