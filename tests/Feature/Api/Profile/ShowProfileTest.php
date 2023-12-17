<?php

use Domain\Pockets\Models\Pocket;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->pocket = Pocket::factory()->create();
    $this->user = User::factory()->create(['pocket_id' => $this->pocket->getKey()]);

    (new UserFactory)->setAmount(4)->create();

    loginApi($this->user);
});

it('can index', function () {
    getJson(route('api.profile.show'))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->where('id', $this->user->getKey())
                    ->has('name')
                    ->has('email')
                    ->has('locale')
                    ->has('sex')
                    ->has('pocket.id')
                    ->has('pocket.balance')
                    ->has('pocket.currency')
                    ->has('pocket.created_at')
                    ->has('pocket.updated_at')
                    ->has('updated_at')
                    ->has('created_at');
            })->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.profile.show'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toContain('select * from `pockets` where `pockets`.`id` in'),
        );

    DB::disableQueryLog();
});
