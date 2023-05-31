<?php

use Database\Seeders\PassportClientsSeeder;
use Domain\Users\Models\User;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seed(PassportClientsSeeder::class);

    $this->user = User::factory()->create();
});

it('cannot refresh token with invalid data', function () {
    postJson(route('passport.token'), [])
        ->assertStatus(400)
        ->assertJson([
            'error' => 'unsupported_grant_type',
            'error_description' => 'The authorization grant type is not supported by the authorization server.',
            'hint' => 'Check that all required parameters have been provided',
            'message' => 'The authorization grant type is not supported by the authorization server.',
        ]);
});

it('cannot login with wrong password', function () {
    postJson(route('passport.token'), [
        'grant_type' => 'refresh_token',
        'client_id' => '994a950e-5d45-4a1e-bbc4-913eb8e1c1dc',
        'client_secret' => 'obDi3CT2tbgdecxrcXIsRS1Zhydyv1rTba2T0tQJ',
        'refresh_token' => 'invalid token',
    ])->assertStatus(401)
        ->assertJson([
            'error' => 'invalid_request',
            'error_description' => 'The refresh token is invalid.',
            'hint' => 'Cannot decrypt the refresh token',
            'message' => 'The refresh token is invalid.',
        ]);
});
