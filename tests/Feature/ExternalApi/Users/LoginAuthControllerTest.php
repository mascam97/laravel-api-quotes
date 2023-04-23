<?php

use Domain\Users\Models\User;
use function Pest\Laravel\postJson;

it('cannot login with invalid data', function () {
    postJson(route('external-api.token-auth'), [
        'email' => 'user@mail.com',
        'password' => 'userPassword',
        'device_name' => $this->faker->userAgent,
    ])->assertUnauthorized()
        ->assertJsonMissingValidationErrors(['device_name', 'email', 'password']);

    postJson(route('external-api.token-auth'), [
        'device_name' => '',
        'email' => '',
        'password' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['device_name', 'email', 'password']);
});

it('can login', function () {
    /** @var User $user */
    $user = User::factory()->create();

    postJson(route('external-api.token-auth'), [
        'email' => $user->email,
        'password' => 'password', // value by default in factory
        'device_name' => $this->faker->userAgent,
    ])->assertOk()
        ->assertSee([
            'Action was executed successfully',
        ]);

    postJson(route('external-api.token-auth'), [
        'email' => $user->email,
        'password' => 'wrong password',
        'device_name' => $this->faker->userAgent,
    ])->assertUnauthorized()
        ->assertSee('The action was unauthorized');
});
