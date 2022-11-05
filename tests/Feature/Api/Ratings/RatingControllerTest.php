<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->fillable = ['score'];
    $this->fields = ['id', 'score', 'qualifier_id', 'qualifier_type', 'rateable_id', 'rateable_type', 'created_at', 'updated_at'];
    $this->table = 'ratings';

    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create(); /* @phpstan-ignore-line */

    $this->rating = new Rating();
    $this->rating->qualifier()->associate($this->user);
    $this->rating->rateable()->associate($this->quote);
    $this->rating->score = 5;
    $this->rating->save();
});

test('guest unauthorized', function () {
    $this->json('GET', route('ratings.index'))
        ->assertUnauthorized();
    $this->json('GET', route('ratings.show', [
        'rating' => $this->rating->id,
    ]))->assertUnauthorized();
    $this->json('POST', route('ratings.quotes.store', [
        'quote' => $this->quote->id,
        'rating' => $this->rating->id,
    ]))->assertUnauthorized();
});

test('index', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('ratings.index'))
        ->assertJsonStructure([
            'data' => ['*' => $this->fields],
        ])->assertOk();
});

test('store validate', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('POST', route('ratings.quotes.store', ['quote' => $this->quote->getKey()]), [
            'score' => '',
        ])->assertJsonValidationErrors($this->fillable);
});

test('store', function () {
    $responseData = $this->actingAs($this->user, 'sanctum')
         ->json('POST', route('ratings.quotes.store', ['quote' => $this->quote->getKey()]), [
             'score' => 4,
         ])->assertJsonMissingValidationErrors($this->fillable)
         ->assertSee('The rating was created successfully')
         ->assertJsonStructure(['data' => $this->fields])
         ->assertCreated()
         ->json('data');

    $this->assertEquals($this->user->getKey(), $responseData['qualifier_id']);
    $this->assertEquals($this->user->getMorphClass(), $responseData['qualifier_type']);
    $this->assertEquals($this->quote->getKey(), $responseData['rateable_id']);
    $this->assertEquals($this->quote->getMorphClass(), $responseData['rateable_type']);
    $this->assertEquals(4, $responseData['score']);
});

test('show 404', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('ratings.show', [
            'rating' => 100000,
        ]))->assertNotFound();
});

test('show', function () {
    $responseData = $this->actingAs($this->user, 'sanctum')
        ->json('GET', route('ratings.show', [
            'rating' => $this->rating->id,
        ]))->assertJsonStructure(['data' => $this->fields])
        ->assertOk()
        ->json('data');

    $this->assertEquals($this->rating->id, $responseData['id']);
});

test('destroy policy', function () {
    $ratingNotOwned = new Rating();
    $ratingNotOwned->qualifier()->associate(User::factory()->create());
    $ratingNotOwned->rateable()->associate($this->quote);
    $ratingNotOwned->score = 5;
    $ratingNotOwned->save();

    $this->actingAs($this->user)
        ->delete(route('ratings.destroy', [
            'rating' => $ratingNotOwned->getKey(),
        ]), ['score' => 3])->assertForbidden();

    $this->assertDatabaseHas($this->table, [
        'id' => $ratingNotOwned->getKey(),
        'score' => 5,
    ]);
});

test('delete 404', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('DELETE', route('ratings.destroy', [
            'rating' => 100000,
        ]))->assertSee([])->assertNotFound();
});

test('delete', function () {
    $this->actingAs($this->user, 'sanctum')
        ->json('DELETE', route('ratings.destroy', [
            'rating' => $this->rating->id,
        ]))->assertSee('The rating was deleted successfully')->assertOk();

    $this->assertDatabaseMissing($this->table, ['id' => $this->rating->id]);
});
