<?php

use Database\Seeders\PassportClientsSeeder;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seed(PassportClientsSeeder::class);

    $this->user = User::factory()->create();
});

it('cannot login with invalid data', function () {
    postJson(route('passport.token'), [])
        ->assertStatus(400)
        ->assertJsonMissingValidationErrors([
            'error' => 'unsupported_grant_type',
            'error_description' => 'The authorization grant type is not supported by the authorization server.',
            'hint' => 'Check that all required parameters have been provided',
            'message' => 'The authorization grant type is not supported by the authorization server.',
        ]);
});

it('cannot login with wrong password', function () {
    postJson(route('passport.token'), [
        'grant_type' => 'password',
        'client_id' => '994a950e-5d45-4a1e-bbc4-913eb8e1c1dc',
        'client_secret' => 'obDi3CT2tbgdecxrcXIsRS1Zhydyv1rTba2T0tQJ',
        'username' => $this->user->email,
        'password' => 'wrong password',
        'scope' => '*',
    ])->assertStatus(400)
        ->assertJson([
            'error' => 'invalid_grant',
            'error_description' => 'The user credentials were incorrect.',
            'message' => 'The user credentials were incorrect.',
        ]);
});
