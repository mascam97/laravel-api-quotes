<?php

namespace Tests\Feature\App\Api\Users\Controllers;

use Domain\Users\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->url_login = 'api/api-token-auth';
    $this->fillable_login = ['device_name', 'email', 'password'];
    $this->url_register = 'api/register';
    $this->fillable_register = ['name', 'email', 'password'];
    $this->table = 'users';
});

test('api_token_auth_validate', function () {
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
});

test('api_token_auth', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->json('POST', $this->url_login, [
        'email' => $user->email,
        'password' => 'password', // value by default in factory
        'device_name' => $this->faker->userAgent,
    ])->assertOk()
        ->assertSee([
            'Action was executed successfully',
            'user', $user->id, $user->email,
        ]);

    $this->json('POST', $this->url_login, [
        'email' => $user->email,
        'password' => 'wrong password',
        'device_name' => $this->faker->userAgent,
    ])->assertUnauthorized()
        ->assertSee('The action was unauthorized');
});

test('register_validate', function () {
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
});

test('register_validate_not_email_duplicated', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->json('POST', $this->url_register, [
        'name' => 'other name',
        'email' => $user->email,
        'password' => 'otherPassword',
    ])->assertStatus(422)
        ->assertSee('The email has already been taken.');
});

test('register', function () {
    $data = [
        'name' => 'new user',
        'email' => 'user@mail.com',
    ];

    $this->json('POST', $this->url_register,
        $data + ['password' => 'userPassword']
    )->assertOk()
        ->assertSee('The user was created successfully');

    $this->assertDatabaseHas($this->table, $data);
});

test('register_password_hashed', function () {
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
});
