<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $fillable = ['email', 'password', 'device_name'];
    private $columns = ['id', 'name', 'email', 'password', 'created_at', 'updated_at'];
    private $table = 'users';

    public function test_validate_login()
    {
        $response = $this->json('POST', 'api/login', [
            'email' => 'user@mail.com',
            'password' => 'userpassword',
            'device_name' => $this->faker->userAgent
        ]);
        $response->assertJsonMissingValidationErrors($this->fillable);

        $response_error = $this->json('POST', 'api/login', [
            'email' => '',
            'password' => '',
            'device_name' => ''
        ]);
        $response_error->assertJsonValidationErrors($this->fillable);
    }

    public function test_login()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', 'api/login', [
            'email' => $user->email,
            'password' => 'password', // value by default in factory
            'device_name' => $this->faker->userAgent
        ]);

        $response->assertStatus(200)
            ->assertSee('Success');

        $response_error = $this->json('POST', 'api/login', [
            'email' => $user->email,
            'password' => 'wrong password',
            'device_name' => $this->faker->userAgent,
        ]);

        $response_error->assertStatus(401)
            ->assertSee('Unauthorized');
    }

    public function test_validate_register()
    {
        $response = $this->json('POST', 'api/register', [
            'name' => 'new user',
            'email' => 'user@mail.com',
            'password' => 'userpassword',
            'device_name' => $this->faker->userAgent,
        ]);
        $response->assertJsonMissingValidationErrors($this->fillable);

        $response_error = $this->json('POST', 'api/register', [
            'name' => '',
            'email' => '134email',
            'password' => '',
            'device_name' => ''
        ]);
        $response_error->assertJsonValidationErrors($this->fillable);
    }

    public function test_register()
    {
        $data = [
            'name' => 'new user',
            'email' => 'user@mail.com',
            'password' => 'userpassword'
        ];

        $response = $this->json(
            'POST',
            'api/register',
            $data + ['device_name' => $this->faker->userAgent]
        );
        $response->assertStatus(200)
            ->assertSee('User created successfully');
        $this->assertDatabaseHas($this->table, $data);

        // When the same email is stored it should make an error because email is unique
        $response_error = $this->json(
            'POST',
            'api/register',
            $data + ['device_name' => $this->faker->userAgent]
        );
        $response_error->assertStatus(500)
            ->assertSee('Integrity constraint violation');
    }
}
