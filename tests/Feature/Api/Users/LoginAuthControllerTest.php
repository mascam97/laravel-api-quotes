<?php

use Domain\Users\Models\User;
use function Pest\Laravel\postJson;

it('cannot login with invalid data', function () {
    postJson(route('api-token-auth'), [
        'email' => 'user@mail.com',
        'password' => 'userPassword',
        'device_name' => $this->faker->userAgent,
    ])->assertJsonMissingValidationErrors(['device_name', 'email', 'password']);

    postJson(route('api-token-auth'), [
        'device_name' => '',
        'email' => '',
        'password' => '',
    ])->assertJsonValidationErrors(['device_name', 'email', 'password']);
});

it('can login', function () {
    /** @var User $user */
    $user = User::factory()->create();

    postJson(route('api-token-auth'), [
        'email' => $user->email,
        'password' => 'password', // value by default in factory
        'device_name' => $this->faker->userAgent,
    ])->assertOk()
        ->assertSee([
            'Action was executed successfully',
            'user', $user->id, $user->email,
        ]);

    postJson(route('api-token-auth'), [
        'email' => $user->email,
        'password' => 'wrong password',
        'device_name' => $this->faker->userAgent,
    ])->assertUnauthorized()
        ->assertSee('The action was unauthorized');
});
