<?php

use Domain\Pockets\Models\Pocket;
use Domain\Users\Actions\SendWelcomeEmailAction;
use Domain\Users\Enums\SexEnum;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertTrue;
use Spatie\QueueableAction\Testing\QueueableActionFake;
use Tests\Factories\RegisterRequestDataFactory;

beforeEach(function () {
    Queue::fake();

    $this->requestData = RegisterRequestDataFactory::new();
});

it('cannot register with invalid data', function () {
    postJson(route('api.register'), [
        'name' => '',
        'email' => '134email',
        'password' => '',
        'birthday' => 'invalid_date',
        'sex' => 'invalid_sex',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors([
            'name' => 'The name field is required.',
            'email' => 'The email must be a valid email address.',
            'password' => 'The password field is required.',
            'birthday' => 'The birthday is not a valid date.',
            'sex' => 'The selected sex is invalid.',
        ]);
});

it('cannot register with duplicated email', function () {
    /** @var User $user */
    $user = User::factory()->create();

    postJson(route('api.register'), $this->requestData->withUser($user)->create())
        ->assertUnprocessable()
        ->assertSee('The email has already been taken.');
});

it('can register', function () {
    config()->set('app.locale', 'en');

    postJson(route('api.register'), $this->requestData->withName('new user in english')->create())
        ->assertOk()
        ->assertSee('The user was created successfully');

    $this->assertDatabaseHas(User::class, [
        'name' => 'new user in english',
        'locale' => 'en_US',
        'sex' => null,
        'birthday' => null,
    ]);

    $this->assertDatabaseHas(Pocket::class, [
        'balance' => 0,
        'currency' => 'USD',
    ]);
});

it('can register with optional fields', function () {
    postJson(route('api.register'),
        $this->requestData->create(['sex' => SexEnum::FEMININE, 'birthday' => '2000-01-01'])
    )->assertOk()
        ->assertSee('The user was created successfully');

    $this->assertDatabaseHas(User::class, [
        'sex' => 'FEMININE',
        'birthday' => '2000-01-01',
    ]);
});

it('can register in spanish locale', function () {
    postJson(
        uri: route('api.register'),
        data: $this->requestData->withName('new user in spanish')->create(),
        headers: ['Accept-Language' => 'es']
    )->assertOk()
        // TODO: This should be 'El' instead of 'La'
        ->assertSee('La usuario fue creado satisfactoriamente');

    $this->assertDatabaseHas(User::class, [
        'name' => 'new user in spanish',
        'locale' => 'es',
    ]);
});

it('hashes password', function () {
    postJson(
        route('api.register'),
        $this->requestData->withPassword('hashed.Pa55word')->create()
    )->assertOk();

    /** @var User $user */
    $user = User::query()->where('email', 'user@mail.com')->first();

    assertTrue(Hash::check('hashed.Pa55word', $user->password));
});

it('processes a job to send a welcome email', function () {
    postJson(route('api.register'))->assertUnprocessable();

    QueueableActionFake::assertNotPushed(SendWelcomeEmailAction::class);

    postJson(route('api.register'), $this->requestData->create())->assertOk();

    QueueableActionFake::assertPushed(SendWelcomeEmailAction::class);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    postJson(route('api.register'), $this->requestData->create())->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(10)
        ->sequence(
            fn ($query) => $query->toBe('select count(*) as aggregate from `users` where `email` = ?'),
            fn ($query) => $query->toBe('insert into `users` (`name`, `email`, `sex`, `birthday`, `password`, `locale`, `email_subscribed_at`, `updated_at`, `created_at`) values (?, ?, ?, ?, ?, ?, ?, ?, ?)'),

            // TODO: Many queries are executed by EventSourcing, look for a way to reduce them
            fn ($query) => $query->toBe('select * from `snapshots` where `aggregate_uuid` = ? order by `id` desc limit 1'),
            fn ($query) => $query->toBe('select * from `stored_events` where `aggregate_uuid` = ? order by `id` asc'),
            fn ($query) => $query->toBe('select max(`aggregate_version`) as aggregate from `stored_events` where `aggregate_uuid` = ?'),
            fn ($query) => $query->toBe('insert into `stored_events` (`event_properties`, `aggregate_uuid`, `aggregate_version`, `event_version`, `event_class`, `meta_data`, `created_at`) values (?, ?, ?, ?, ?, ?, ?)'),
            fn ($query) => $query->toBe('update `stored_events` set `meta_data` = ? where `id` = ?'),
            fn ($query) => $query->toBe('insert into `pockets` (`balance`, `currency`, `updated_at`, `created_at`) values (?, ?, ?, ?)'),
            fn ($query) => $query->toBe('select * from `users` where `users`.`id` = ? and `users`.`deleted_at` is null limit 1'),
            fn ($query) => $query->toBe('update `users` set `pocket_id` = ?, `users`.`updated_at` = ? where `id` = ?'),
        );

    DB::disableQueryLog();
});
