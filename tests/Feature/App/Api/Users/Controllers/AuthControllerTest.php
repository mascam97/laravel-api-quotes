<?php

namespace Tests\Feature\App\Api\Users\Controllers;

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $url_login = 'api/api-token-auth';

    private array $fillable_login = ['device_name', 'email', 'password'];

    private string $url_register = 'api/register';

    private array $fillable_register = ['name', 'email', 'password'];

    private string $table = 'users';

    public function test_api_token_auth_validate(): void
    {
        $this->json('POST', $this->url_login, [
            'email' => 'user@mail.com',
            'password' => 'userPassword',
            'device_name' => $this->faker->userAgent,
        ])->assertJsonMissingValidationErrors($this->fillable_login);

        $this->json('POST', $this->url_login, [
            'device_name' => '',
            'email' => '',
            'password' => '',
        ])->assertJsonValidationErrors($this->fillable_login);
    }

    public function test_api_token_auth(): void
    {
        /** @var User $user */
        $user = (new UserFactory)->create();

        $this->json('POST', $this->url_login, [
            'email' => $user->email,
            'password' => 'password', // value by default in factory
            'device_name' => $this->faker->userAgent,
        ])->assertOk()
            ->assertSee([
                'Action was executed successfully',
                'user_logged', $user->id, $user->email,
            ]);

        $this->json('POST', $this->url_login, [
            'email' => $user->email,
            'password' => 'wrong password',
            'device_name' => $this->faker->userAgent,
        ])->assertUnauthorized()
            ->assertSee('The action was unauthorized');
    }

    public function test_register_validate(): void
    {
        $this->json('POST', $this->url_register, [
            'name' => 'new user',
            'email' => 'user@mail.com',
            'password' => 'userPassword',
            'device_name' => $this->faker->userAgent,
        ])->assertJsonMissingValidationErrors($this->fillable_register);

        $this->json('POST', $this->url_register, [
            'name' => '',
            'email' => '134email',
            'password' => '',
            'device_name' => '',
        ])->assertJsonValidationErrors($this->fillable_register)
            ->assertSee('The name field is required. (and 2 more errors)');
    }

    public function test_register_validate_not_email_duplicated(): void
    {
        /** @var User $user */
        $user = (new UserFactory)->create();

        $this->json('POST', $this->url_register, [
            'name' => 'other name',
            'email' => $user->email,
            'password' => 'otherPassword',
        ])->assertStatus(422)
            ->assertSee('The email has already been taken.');
    }

    public function test_register(): void
    {
        $data = [
            'name' => 'new user',
            'email' => 'user@mail.com',
        ];

        $this->json('POST', $this->url_register,
            $data + ['password' => 'userPassword']
        )->assertOk()
            ->assertSee('The user was created successfully');

        $this->assertDatabaseHas($this->table, $data);
    }

    public function test_register_password_hashed(): void
    {
        $this->json('POST', $this->url_register, [
            'name' => 'new user',
            'email' => 'user@mail.com',
            'password' => 'userPassword',
        ])
            ->assertOk();

        /** @var User $user */
        $user = User::query()->where('email', 'user@mail.com')->first();

        $this->assertTrue(Hash::check(
                'userPassword',
                $user->password,
            ));
    }
}
