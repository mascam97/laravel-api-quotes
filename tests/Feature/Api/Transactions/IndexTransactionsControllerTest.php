<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    loginApi($this->user);
});

it('can index', function () {
    getJson(route('api.transactions.index'))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 0)->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.transactions.index'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(0);

    DB::disableQueryLog();
});
