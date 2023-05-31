<?php

use Database\Seeders\PassportClientsSeeder;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seed(PassportClientsSeeder::class);

    $this->user = User::factory()->create();
});

it('can login with users provider', function () {
    postJson(route('passport.token'), [
        'grant_type' => 'password',
        'client_id' => '994a950e-5d45-4a1e-bbc4-913eb8e1c1dc',
        'client_secret' => 'obDi3CT2tbgdecxrcXIsRS1Zhydyv1rTba2T0tQJ',
        'username' => $this->user->email,
        'password' => 'password',
        'scope' => '*',
    ])->assertOk()
        ->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token',
        ]);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    postJson(route('passport.token'), [
        'grant_type' => 'password',
        'client_id' => '994a950e-5d45-4a1e-bbc4-913eb8e1c1dc',
        'client_secret' => 'obDi3CT2tbgdecxrcXIsRS1Zhydyv1rTba2T0tQJ',
        'username' => $this->user->email,
        'password' => 'password',
        'scope' => '*',
    ])->assertOk();

    expect(formatQueries(DB::getQueryLog()))->toHaveCount(5);

    DB::disableQueryLog();
});
