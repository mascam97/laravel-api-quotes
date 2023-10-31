<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    loginApiAnalytics($this->user);
});

it('can index', function () {
    postJson(route('graphql'), [
        'query' => '{
            users {
                id
                name
                email
                created_at
                updated_at
                deleted_at
                isMe
            }
        }',
    ])->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data.users', 5, fn ($json) => $json
                ->has('id')
                ->has('name')
                ->has('email')
                ->has('created_at')
                ->has('updated_at')
                ->has('deleted_at')
                ->has('isMe')
            )->etc();
        });
});

test('it can get by id', function () {
    /** @var User $user */
    $user = (new UserFactory)->create();

    postJson(route('graphql'), [
        'query' => "{
            users(id: {$user->getKey()} ) {
                id
                name
                email
                created_at
            }
        }",
    ])->assertOk()
        ->assertJson(function (AssertableJson $json) use ($user) {
            $json->has('data.users', 1, fn ($json) => $json
                ->where('id', $user->getKey())
                ->where('name', $user->name)
                ->where('email', $user->email)
                ->has('created_at')
            )->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    postJson(route('graphql'), [
        'query' => '{
            users {
                id
                name
            }
        }',
    ])->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toBe('select `users`.`id`, `users`.`name` from `users` where `users`.`deleted_at` is null'),
        );

    DB::disableQueryLog();
});
