<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_employee_can_view_all_employees()
    {
        # create manager
        $manager = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        # login as admin
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

        # logout
        $this->postJson('/api/logout', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        # create employee
        $employee = $this->postJson('/api/register', [
            'name' => 'Test Employee',
            'email' => 'testemployee@mail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        # login as manager
        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        # add employee
        $company_employee = $this->postJson('/api/'.$company['data']['id'].'/employee', [
            'user_id' => $employee['data']['id']
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        # logout
        $this->postJson('/api/logout', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        # login as employee
        $login = $this->postJson('/api/login', [
            'email' => 'testemployee@mail.com',
            'password' => 'password',
        ]);

        # view employees
        $response = $this->getJson('/api/'.$company['data']['id'].'/employee?order_by=name&order_direction=asc&limit=1&offset=0', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => [
                        $company_employee['data']
                    ]
                ]);
    }

    public function test_employee_can_view_details_of_employee()
    {
        # create manager
        $manager = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        # login as admin
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

        # logout
        $this->postJson('/api/logout', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        # create employee
        $employee = $this->postJson('/api/register', [
            'name' => 'Test Employee',
            'email' => 'testemployee@mail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        # login as manager
        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        # add employee
        $company_employee = $this->postJson('/api/'.$company['data']['id'].'/employee', [
            'user_id' => $employee['data']['id']
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        # logout
        $this->postJson('/api/logout', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        # login as employee
        $login = $this->postJson('/api/login', [
            'email' => 'testemployee@mail.com',
            'password' => 'password',
        ]);

        # view employee
        $response = $this->getJson('/api/'.$company['data']['id'].'/employee/'.$company_employee['data']['id'], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => $company_employee['data']
                ]);
    }
}
