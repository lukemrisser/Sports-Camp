<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Player;
use App\Models\Camp;
use App\Models\User;
use Tests\TestCase;

class ParentSignUpTest extends TestCase
{
    // Refresh the database for each run
    use RefreshDatabase;

    // Test out the Camper Registration Workflow
    public function test_camper_registration_creates_all_records_and_redirects_to_payment() {

        $user = User::factory()->create([
            'fname' => 'Test',
            'lname' => 'User',
            'email' => 'test@example.com'
        ]);

        $response = $this->actingAs($user)->post(route('parent.store'),[
            'Phone'=> '1234567890',
            'Address' => '123 Test St',
            'City' => 'Mechanicsburg',
            'State' => 'PA',
            'Postal_Code' => '542321',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('Parents', [
            'Email' => 'test@example.com',
            'Phone' => '1234567890',
            'Parent_FirstName' => 'Test',
            'Parent_LastName' => 'User',

        ]);
    }
}