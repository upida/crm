<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_admin_can_create_company()
    {
        # create manager
        $manager = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'superadmin@example.com',
            'password' => 'supeR@-',
        ]);

        $response = $this->postJson('/api/company', [
            'name' => 'Test Company',
            'email' => 'testcompany@mail.com',
            'phone' => '08987654321',
            'manager' => [
                'user_id' => $manager['data']['id']
            ]
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'Company created successfully',
                    'data' => [
                        'name' => 'Test Company',
                        'email' => 'testcompany@mail.com',
                        'phone' => '08987654321'
                    ]
                ]);
    }

    public function test_no_admin_cannot_create_company()
    {
        # create manager
        $manager = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/company', [
            'name' => 'Test Company',
            'email' => 'testcompany@mail.com',
            'phone' => '08987654321',
            'manager' => [
                'user_id' => $manager['data']['id']
            ]
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_show_company()
    {
        # create manager
        $manager = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'superadmin@example.com',
            'password' => 'supeR@-',
        ]);

        # create company
        $company = $this->postJson('/api/company', [
            'name' => 'Test Company',
            'email' => 'testcompany@mail.com',
            'phone' => '08987654321',
            'manager' => [
                'user_id' => $manager['data']['id']
            ]
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response = $this->getJson('/api/company/'.$company['data']['id'], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => [
                        'name' => 'Test Company',
                        'email' => 'testcompany@mail.com',
                        'phone' => '08987654321',
                        'created_at' => $company['data']['created_at'],
                        'updated_at' => $company['data']['updated_at'],
                        'deleted_at' => NULL
                    ]
                ]);
    }

    public function test_admin_can_update_company()
    {
        # create manager
        $manager = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'superadmin@example.com',
            'password' => 'supeR@-',
        ]);

        # create company
        $company = $this->postJson('/api/company', [
            'name' => 'Test Company',
            'email' => 'testcompany@mail.com',
            'phone' => '08987654321',
            'manager' => [
                'user_id' => $manager['data']['id']
            ]
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response = $this->putJson('/api/company/'.$company['data']['id'], [
            'name' => 'Test Company Updated',
            'email' => 'testcompany@mail.com',
            'phone' => '08987654321',
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'Test Company Updated updated successfully',
                    'data' => [
                        'name' => 'Test Company Updated',
                        'email' => 'testcompany@mail.com',
                        'phone' => '08987654321',
                        'created_at' => $company['data']['created_at'],
                        'updated_at' => $response['data']['updated_at'],
                        'deleted_at' => NULL
                    ]
                ]);
    }

    public function test_admin_can_delete_company()
    {
        # create manager
        $manager = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'superadmin@example.com',
            'password' => 'supeR@-',
        ]);

        # create company
        $company = $this->postJson('/api/company', [
            'name' => 'Test Company',
            'email' => 'testcompany@mail.com',
            'phone' => '08987654321',
            'manager' => [
                'user_id' => $manager['data']['id']
            ]
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response = $this->deleteJson('/api/company/'.$company['data']['id'], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'message' => $company['data']['name'].' deleted successfully',
                ]);
    }
}
