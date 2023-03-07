<?php

use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
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

test('sql queries optimization test', function () {
    /** @var User $user */
    $user = User::factory()->create();

    DB::enableQueryLog();

    postJson(route('api-token-auth'), [
        'email' => $user->email,
        'password' => 'password', // value by default in factory
        'device_name' => $this->faker->userAgent,
    ])->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select * from `users` where `email` = ? limit 1'),
            fn ($query) => $query->toBe('insert into `personal_access_tokens` (`name`, `token`, `abilities`, `expires_at`, `tokenable_id`, `tokenable_type`, `updated_at`, `created_at`) values (?, ?, ?, ?, ?, ?, ?, ?)'),
        );

    DB::disableQueryLog();
});
