<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    login($this->user);
});

it('can index', function () {
    getJson(route('api.me'))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->where('id', $this->user->getKey())
                    ->has('name')
                    ->has('email')
                    ->has('created_at');
            })->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.me'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(0);

    DB::disableQueryLog();
});
