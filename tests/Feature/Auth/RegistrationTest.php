<?php

namespace Tests\Feature\Auth;
use App\Models\Camp;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase {
    public function test_registration_screen_can_be_rendered(): void {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void {
        $response = $this->get('/register');

        $response = $this->post('/register', [
            'fname' => 'Test',
            'lname' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/verify-email');
    }
}