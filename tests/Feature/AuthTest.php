<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'User registered successfully']);
    }

    public function test_user_can_login()
    {
        $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    public function test_user_can_get_profile()
    {
        $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->getJson('/api/profile', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['name', 'email']);
    }

    public function test_user_admin_can_get_profile()
    {
        $login = $this->postJson('/api/login', [
            'email' => 'superadmin@example.com',
            'password' => 'supeR@-',
        ]);

        $response = $this->getJson('/api/profile', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['name', 'email']);
    }

    public function test_user_can_update_profile()
    {
        $login = $this->postJson('/api/login', [
            'email' => 'superadmin@example.com',
            'password' => 'supeR@-',
        ]);

        $response = $this->putJson('/api/profile', [
            'name' => 'Super Admin Updated',
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User updated successfully',
                    'data' => [
                        'name' => 'Super Admin Updated',
                        'email' => 'superadmin@example.com',
                        'role' => 'admin',
                    ]
                ]);
    }

    public function test_user_can_logout()
    {
        $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Successfully logged out']);
    }
}
