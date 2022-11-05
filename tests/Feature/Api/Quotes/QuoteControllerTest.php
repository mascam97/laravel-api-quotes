<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->fillable = ['title', 'content'];
    $this->fields = ['id', 'title', 'content', 'state', 'excerpt', 'created_at', 'updated_at'];
    $this->table = 'quotes';

    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create(); /* @phpstan-ignore-line */
});

test('guest unauthorized', function () {
    $this->json('GET', route('quotes.index'))
        ->assertUnauthorized();                // index
    $this->json('GET', route('quotes.show', [
        'quote' => $this->quote->id,
    ]))->assertUnauthorized();     // show
    $this->json('POST', route('quotes.index'))
        ->assertUnauthorized();           // store
    $this->json('PUT', route('quotes.show', [
        'quote' => $this->quote->id,
    ]))->assertUnauthorized(); // update
    $this->json('DELETE', route('quotes.show', [
        'quote' => $this->quote->id,
    ]))->assertUnauthorized();  // destroy
});

test('index', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('quotes.index'))
        ->assertJsonStructure([
            'data' => ['*' => $this->fields],
        ])->assertOk();
});

test('store validate', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('POST', route('quotes.index'), [
            'title' => '',
            'content' => '',
        ])->assertJsonValidationErrors($this->fillable);
});

test('store', function () {
    $data = [
        'title' => $this->faker->sentence,
        'content' => $this->faker->text(500),
    ];

    $this->actingAs($this->user, 'sanctum')
        ->json('POST', route('quotes.index'), $data)
        ->assertJsonMissingValidationErrors($this->fillable)
        ->assertSee('The quote was created successfully')
        ->assertJsonStructure(['data' => $this->fields])
        ->assertJson(['data' => $data])
        ->assertCreated();

    $this->assertDatabaseHas($this->table, $data);
});

test('show 404', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('quotes.show', [
            'quote' => 100000,
        ]))->assertNotFound();
});

test('show', function () {
    $responseData = $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('quotes.show', [
            'quote' => $this->quote->id,
        ]))->assertJsonStructure(['data' => $this->fields])
        ->assertOk()
        ->json('data');

    $this->assertEquals($this->quote->id, $responseData['id']);
    $this->assertEquals($this->quote->content, $responseData['content']);
});

test('update policy', function () {
    /** @var User $userNotOwner */
    $userNotOwner = User::factory()->create();
    // just the owner $this->user can delete his quote

    $this->actingAs($userNotOwner)
        ->put(route('quotes.show', [
            'quote' => $this->quote->id,
        ]), [
            'title' => 'new title not allowed',
            'content' => 'new content not allowed',
        ])->assertForbidden();

    $this->assertDatabaseHas($this->table, [
        'title' => $this->quote->title,
        'content' => $this->quote->content,
    ]);
    $this->assertDatabaseMissing($this->table, [
        'title' => 'new title not allowed',
        'content' => 'new content not allowed',
    ]);
});

test('update', function () {
    $new_data = [
        'title' => 'new title',
        'content' => 'new content',
    ];

    $this->actingAs($this->user, 'sanctum')
        ->json('PUT', route('quotes.show', [
            'quote' => $this->quote->id,
        ]), $new_data)->assertJsonMissingValidationErrors($this->fillable)
        ->assertSee('The quote was updated successfully')
        ->assertJsonStructure(['data' => $this->fields])
        ->assertJson(['data' => $new_data])
        ->assertOk();

    $this->assertDatabaseMissing($this->table, ['id' => $this->quote->id, 'title' => $this->quote->title]);
    $this->assertDatabaseHas($this->table, ['id' => $this->quote->id, 'title' => 'new title']);
});

test('destroy policy', function () {
    /** @var User $UserNotOwner */
    $UserNotOwner = User::factory()->create();

    $this->actingAs($UserNotOwner)
        ->delete(route('quotes.show', [
            'quote' => $this->quote->id,
        ]))->assertForbidden();

    $this->assertDatabaseHas($this->table, [
        'title' => $this->quote->title,
        'content' => $this->quote->content,
    ]);
});

test('delete 404', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('DELETE', route('quotes.show', [
            'quote' => 100000,
        ]))->assertSee([])->assertNotFound();
});

test('delete', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('DELETE', route('quotes.show', [
            'quote' => $this->quote->id,
        ]))->assertSee('The quote was deleted successfully')->assertOk();

    $this->assertDatabaseMissing($this->table, ['id' => $this->quote->id]);
});
