<?php

use Database\Seeders\PassportClientsSeeder;
use Domain\Users\Models\User;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seed(PassportClientsSeeder::class);

    $this->user = User::factory()->create();
});

it('can refresh token', function () {
    // TODO: Create token by factory
    $refreshToken = postJson(route('passport.token'), [
        'grant_type' => 'password',
        'client_id' => '994a950e-5d45-4a1e-bbc4-913eb8e1c1dc',
        'client_secret' => 'obDi3CT2tbgdecxrcXIsRS1Zhydyv1rTba2T0tQJ',
        'username' => $this->user->email,
        'password' => 'password',
        'scope' => '*',
    ])->assertOk()
        ->json('refresh_token');

    postJson(route('passport.token'), [
        'grant_type' => 'refresh_token',
        'client_id' => '994a950e-5d45-4a1e-bbc4-913eb8e1c1dc',
        'client_secret' => 'obDi3CT2tbgdecxrcXIsRS1Zhydyv1rTba2T0tQJ',
        'refresh_token' => $refreshToken,
    ])->assertOk()
        ->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token',
        ]);
});
