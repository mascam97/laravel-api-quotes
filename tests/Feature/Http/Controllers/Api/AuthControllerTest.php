<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $url_login = 'api/api-token-auth';
    private $fillable_login = ['device_name', 'email', 'password'];

    private $url_register = 'api/register';
    private $fillable_register = ['name', 'email', 'password'];

    private $columns = ['id', 'name', 'email', 'password', 'created_at', 'updated_at'];
    private $table = 'users';

    public function test_api_token_auth_validate()
    {
        $response = $this->json('POST', $this->url_login, [
            'email' => 'user@mail.com',
            'password' => 'userpassword',
            'device_name' => $this->faker->userAgent
        ]);
        $response->assertJsonMissingValidationErrors($this->fillable_login);

        $response_error = $this->json('POST', $this->url_login, [
            'device_name' => '',
            'email' => '',
            'password' => ''
        ]);
        $response_error->assertJsonValidationErrors($this->fillable_login);
    }

    public function test_api_token_auth()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', $this->url_login, [
            'email' => $user->email,
            'password' => 'password', // value by default in factory
            'device_name' => $this->faker->userAgent
        ]);

        $response->assertStatus(200)
            ->assertSee(['Success', 'user_logged', $user->id, $user->name, $user->email]);

        $response_error = $this->json('POST', $this->url_login, [
            'email' => $user->email,
            'password' => 'wrong password',
            'device_name' => $this->faker->userAgent,
        ]);

        $response_error->assertStatus(401)
            ->assertSee('Unauthorized');
    }

    public function test_register_validate()
    {
        $response = $this->json('POST', $this->url_register, [
            'name' => 'new user',
            'email' => 'user@mail.com',
            'password' => 'userpassword',
            'device_name' => $this->faker->userAgent,
        ]);
        $response->assertJsonMissingValidationErrors($this->fillable_register);

        $response_error = $this->json('POST', $this->url_register, [
            'name' => '',
            'email' => '134email',
            'password' => '',
            'device_name' => ''
        ]);
        $response_error->assertJsonValidationErrors($this->fillable_register)
            ->assertSee('The given data was invalid.');
    }

    public function test_register_validate_not_email_duplicated()
    {
        $user = User::factory()->create();

        $response = $this->json(
            'POST',
            $this->url_register,
            [
                'name' => 'other name',
                'email' => $user->email,
                'password' => 'other password',
            ]
        );

        $response->assertStatus(422)
            ->assertSee('The email has already been taken.');
    }

    public function test_register()
    {
        $data = [
            'name' => 'new user',
            'email' => 'user@mail.com'
        ];

        $response = $this->json(
            'POST',
            $this->url_register,
            $data + ['password' => 'userpassword']
        );
        $response->assertStatus(200)
            ->assertSee('User created successfully');
        $this->assertDatabaseHas($this->table, $data);
    }

    public function test_register_password_hashed()
    {
        $data = [
            'name' => 'new user',
            'email' => 'user@mail.com',
            'password' => 'userpassword'
        ];

        $response = $this->json(
            'POST',
            $this->url_register,
            $data
        );

        $response->assertStatus(200);
        $this->assertTrue(
            Hash::check(
                $data['password'],
                User::where('email', $data['email'])->first()->password
            )
        );
    }
}
