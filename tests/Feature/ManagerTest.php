<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManagerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_automatically_add_manager_to_company()
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

        $response = $this->getJson('/api/'.$company['data']['id'].'/manager', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => [
                        $manager['data']
                    ]
                ]);
    }

    public function test_manager_can_add_employee()
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

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/'.$company['data']['id'].'/employee', [
            'user_id' => $employee['data']['id']
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    "message" => "Employee {$employee['data']['name']} created successfully",
                    'data' => [
                        'id' => $response['data']['id'],
                        'company_id' => $company['data']['id'],
                        'user_id' => $employee['data']['id'],
                        'user' => array_merge($employee['data'], [
                            'role' => 'employee',
                        ])
                    ]
                ]);
    }

    public function test_manager_can_update_employee()
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

        # update employee
        $response = $this->putJson("/api/{$company['data']['id']}/employee/{$company_employee['data']['id']}", [
            'name' => 'Test Employee Updated',
        ], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    "message" => "Employee Test Employee Updated updated successfully",
                    'data' => [
                        'id' => $company_employee['data']['id'],
                        'company_id' => $company['data']['id'],
                        'user_id' => $employee['data']['id'],
                        'user' => array_merge($employee['data'], [
                            'name' => 'Test Employee Updated',
                            'role' => 'employee',
                            'updated_at' => $response['data']['updated_at'],
                        ])
                    ]
                ]);
    }

    public function test_manager_can_delete_employee()
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

        # delete employee
        $response = $this->deleteJson('/api/'.$company['data']['id'].'/employee/'.$company_employee['data']['id'], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    "message" => "Employee {$employee['data']['name']} deleted successfully",
                ]);
    }

    public function test_manager_can_view_all_managers()
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

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        # view managers
        $response = $this->getJson('/api/'.$company['data']['id'].'/manager?order_by=name&order_direction=asc&limit=1&offset=0', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => [
                        $manager['data']
                    ]
                ]);
    }

    public function test_manager_can_view_all_employees()
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

    public function test_manager_can_view_details_of_employee()
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

        # create employee
        $employee = $this->postJson('/api/register', [
            'name' => 'Test Employee',
            'email' => 'testemployee@mail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        # logout
        $this->postJson('/api/logout', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

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

        # view employee
        $response = $this->getJson('/api/'.$company['data']['id'].'/employee/'.$company_employee['data']['id'], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => $company_employee['data']
                ]);
    }

    public function test_manager_can_view_details_of_manager()
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

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        # view manager
        $response = $this->getJson('/api/'.$company['data']['id'].'/manager/'.$company['data']['managers'][0]['id'], [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => $company['data']['managers'][0]
                ]);
    }

    public function test_manager_can_search_managers()
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

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        # view managers
        $response = $this->getJson('/api/'.$company['data']['id'].'/manager?search=Test&order_by=name&order_direction=asc&limit=1&offset=0', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => [
                        $manager['data']
                    ]
                ]);
    }

    public function test_manager_can_search_employees()
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

        # view employees
        $response = $this->getJson('/api/'.$company['data']['id'].'/employee?search=Test&order_by=name&order_direction=asc&limit=1&offset=0', [
            'Authorization' => 'Bearer '.$login['token']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => [
                        $company_employee['data']
                    ]
                ]);
    }

}
