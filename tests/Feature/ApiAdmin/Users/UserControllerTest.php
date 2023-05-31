<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    (new QuoteFactory)->withUser($this->user)->create();
});

it('cannot authorize guest', function () {
    getJson(route('admin.users.index'))
        ->assertUnauthorized();

    getJson(route('admin.users.show', ['user' => $this->user->id]))
        ->assertUnauthorized();

    deleteJson(route('admin.users.show', ['user' => $this->user->id]))
        ->assertUnauthorized();

    getJson(route('admin.me'))
        ->assertUnauthorized();
});

it('requires permission', function () {
    loginApiAdmin($this->user);

    getJson(route('admin.users.index'))
        ->assertForbidden();

    getJson(route('admin.users.show', ['user' => $this->user->id]))
        ->assertForbidden();

    deleteJson(route('admin.users.show', ['user' => $this->user->id]))
        ->assertForbidden();
});

it('cannot show undefined data', function () {
    giveRoleWithPermission($this->user, 'view users');
    loginApiAdmin($this->user);

    getJson(route('admin.users.show', ['user' => 100000]))
        ->assertNotFound();

    deleteJson(route('admin.users.show', ['user' => 100000]))
        ->assertNotFound();
});
