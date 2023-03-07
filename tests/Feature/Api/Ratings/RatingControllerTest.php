<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->fields = ['id', 'score', 'qualifier_id', 'qualifier_type', 'rateable_id', 'rateable_type', 'created_at', 'updated_at'];

    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create(); /* @phpstan-ignore-line */

    $this->rating = new Rating();
    $this->rating->qualifier()->associate($this->user);
    $this->rating->rateable()->associate($this->quote);
    $this->rating->score = 5;
    $this->rating->save();
});

it('cannot authorize guest', function () {
    getJson(route('ratings.index'))
        ->assertUnauthorized();

    getJson(route('ratings.show', [
        'rating' => $this->rating->id,
    ]))->assertUnauthorized();

    postJson(route('ratings.quotes.store', [
        'quote' => $this->quote->id,
        'rating' => $this->rating->id,
    ]))->assertUnauthorized();
});

it('cannot store invalid data', function () {
    login($this->user);

    postJson(route('ratings.quotes.store', ['quote' => $this->quote->getKey()]), [
        'score' => '',
    ])->assertJsonValidationErrors(['score']);
});

it('can store', function () {
    login($this->user);

    $responseData = postJson(
        route('ratings.quotes.store', ['quote' => $this->quote->getKey()]),
        ['score' => 4]
    )->assertJsonMissingValidationErrors(['score'])
         ->assertSee('The rating was created successfully')
         ->assertJsonStructure(['data' => $this->fields])
         ->assertCreated()
         ->json('data');

    expect($responseData)
        ->qualifier_id->toEqual($this->user->getKey())
        ->qualifier_type->toBe($this->user->getMorphClass())
        ->rateable_id->toEqual($this->quote->getKey())
        ->rateable_type->toBe($this->quote->getMorphClass())
        ->score->toBe(4);
});

it('cannot show undefined data', function () {
    login($this->user);

    getJson(route('ratings.show', [
        'rating' => 100000,
    ]))->assertNotFound();
});

it('cannot destroy data by not owner', function () {
    $ratingNotOwned = new Rating();
    $ratingNotOwned->qualifier()->associate(User::factory()->create());
    $ratingNotOwned->rateable()->associate($this->quote);
    $ratingNotOwned->score = 5;
    $ratingNotOwned->save();

    login($this->user);

    deleteJson(route('ratings.destroy', [
        'rating' => $ratingNotOwned->getKey(),
    ]), ['score' => 3])->assertForbidden();

    $this->assertDatabaseHas(Rating::class, [
        'id' => $ratingNotOwned->getKey(),
        'score' => 5,
    ]);
});

it('cannot delete undefined data', function () {
    login($this->user);

    deleteJson(route('ratings.destroy', ['rating' => 100000]))
        ->assertSee([])
        ->assertNotFound();
});

it('can delete', function () {
    login($this->user);

    deleteJson(route('ratings.destroy', ['rating' => $this->rating->id]))
        ->assertSee('The rating was deleted successfully')
        ->assertOk();

    assertDatabaseMissing(Rating::class, ['id' => $this->rating->id]);
});
