<?php

namespace Tests\Feature\App\Api\Users\Controllers;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    (new QuoteFactory)->withUser($this->user)->create();
});

test('guest unauthorized', function () {
    $this->json('GET', route('users.index'))
        ->assertUnauthorized();

    $this->json('GET', route('users.index', [
        'user' => $this->user->id,
    ]))->assertUnauthorized();
});

test('index', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('users.index'))
        ->assertJsonStructure([
            'data' => ['*' => ['id', 'name', 'email', 'created_at']],
        ])->assertOk();
});

test('show 404', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('users.show', [
            'user' => 100000,
        ]))->assertNotFound();
});

test('show', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('users.index', [
            'user' => $this->user->id,
        ]))->assertSee([$this->user->id, $this->user->name])
        ->assertOk();
});
