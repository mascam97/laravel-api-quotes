<?php

use App\Jobs\Users\SendWelcomeEmail;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertTrue;
use Tests\Factories\RegisterRequestDataFactory;

beforeEach(function () {
    Queue::fake();
    Bus::fake();

    $this->requestData = RegisterRequestDataFactory::new();
});

it('cannot register with invalid data', function () {
    postJson(route('register'), [
        'name' => '',
        'email' => '134email',
        'password' => '',
        'device_name' => '',
    ])->assertJsonValidationErrors(['name', 'email', 'password'])
        ->assertSee('The name field is required. (and 2 more errors)');
});

it('cannot register with duplicated email', function () {
    /** @var User $user */
    $user = User::factory()->create();

    postJson(route('register'), $this->requestData->withUser($user)->create())
        ->assertStatus(422)
        ->assertSee('The email has already been taken.');
});

it('can register', function () {
    config()->set('app.locale', 'en');

    postJson(route('register'),
        $this->requestData->withName('new user in english')->create()
    )->assertOk()
        ->assertSee('The user was created successfully');

    $this->assertDatabaseHas(User::class, [
        'name' => 'new user in english',
        'locale' => 'en_US',
    ]);
});

it('can register in spanish locale', function () {
    postJson(
        uri: route('register'),
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
        route('register'),
        $this->requestData->withPassword('hashedPassword')->create()
    )->assertOk();

    /** @var User $user */
    $user = User::query()->where('email', 'user@mail.com')->first();

    assertTrue(Hash::check('hashedPassword', $user->password));
});

it('processes a job to send a welcome email', function () {
    postJson(route('register'))->assertUnprocessable();

    Bus::assertNotDispatched(SendWelcomeEmail::class);

    postJson(route('register'), $this->requestData->create())
        ->assertOk();

    Bus::assertDispatched(SendWelcomeEmail::class);
});
