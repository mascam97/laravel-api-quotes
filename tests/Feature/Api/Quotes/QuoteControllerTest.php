<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create(); /* @phpstan-ignore-line */
});

it('cannot authorize guest', function () {
    getJson(route('api.quotes.index'))
        ->assertUnauthorized();

    getJson(route('api.quotes.show', ['quote' => $this->quote->id]))
        ->assertUnauthorized();

    postJson(route('api.quotes.store'))
        ->assertUnauthorized();

    putJson(route('api.quotes.update', ['quote' => $this->quote->id]))
        ->assertUnauthorized();

    deleteJson(route('api.quotes.destroy', ['quote' => $this->quote->id]))
        ->assertUnauthorized();

    getJson(route('api.me.quotes'))
        ->assertUnauthorized();
});

it('cannot store invalid data', function () {
    loginApi();

    postJson(route('api.quotes.index'), [
        'title' => '',
        'content' => '',
    ])->assertJsonValidationErrors(['title', 'content']);
});

it('can store', function () {
    $data = [
        'title' => $this->faker->sentence,
        'content' => $this->faker->text(500),
    ];

    loginApi();

    postJson(route('api.quotes.index'), $data)
        ->assertJsonMissingValidationErrors(['title', 'content'])
        ->assertSee('The quote was created successfully')
        ->assertJsonStructure([
            'data' => ['id', 'title', 'content', 'state', 'average_rating', 'excerpt', 'created_at', 'updated_at'], ])
        ->assertJson(['data' => $data])
        ->assertCreated();

    assertDatabaseHas(Quote::class, $data);
});

it('cannot show undefined data', function () {
    loginApi();

    getJson(route('api.quotes.show', ['quote' => 100000]))
        ->assertNotFound();
});

it('cannot update data from not owner', function () {
    $userNotOwner = User::factory()->create();
    loginApi($userNotOwner);

    putJson(route('api.quotes.update', ['quote' => $this->quote->id]), [
        'title' => 'new title not allowed',
        'content' => 'new content not allowed',
    ])->assertForbidden();

    assertDatabaseHas(Quote::class, [
        'title' => $this->quote->title,
        'content' => $this->quote->content,
    ]);
    assertDatabaseMissing(Quote::class, [
        'title' => 'new title not allowed',
        'content' => 'new content not allowed',
    ]);
});

it('can update', function () {
    $newData = [
        'title' => 'new title',
        'content' => 'new content',
    ];

    loginApi($this->user);

    putJson(route('api.quotes.update', ['quote' => $this->quote->id]), $newData)
        ->assertJsonMissingValidationErrors(['title', 'content'])
        ->assertSee('The quote was updated successfully')
        ->assertJsonStructure([
            'data' => ['id', 'title', 'content', 'state', 'average_rating', 'excerpt', 'created_at', 'updated_at'],
        ])->assertJson(['data' => $newData])
        ->assertOk();

    assertDatabaseMissing(Quote::class, ['id' => $this->quote->id, 'title' => $this->quote->title]);
    assertDatabaseHas(Quote::class, ['id' => $this->quote->id, 'title' => 'new title']);
});

it('cannot destroy data from not owner', function () {
    /** @var User $UserNotOwner */
    $UserNotOwner = User::factory()->create();
    loginApi($UserNotOwner);

    deleteJson(route('api.quotes.destroy', ['quote' => $this->quote->id]))
        ->assertForbidden();

    assertDatabaseHas(Quote::class, [
        'title' => $this->quote->title,
        'content' => $this->quote->content,
    ]);
});

it('cannot delete undefined data', function () {
    loginApi();

    deleteJson(route('api.quotes.destroy', ['quote' => 100000]))
        ->assertSee([])->assertNotFound();
});

it('can delete', function () {
    loginApi($this->user);

    deleteJson(route('api.quotes.destroy', ['quote' => $this->quote->id]))
        ->assertSee('The quote was deleted successfully')->assertOk();

    assertDatabaseMissing(Quote::class, ['id' => $this->quote->id]);
});
