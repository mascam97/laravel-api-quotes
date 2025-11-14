<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    $this->rating = new Rating();
    $this->rating->qualifier()->associate($this->user);
    $this->rating->rateable()->associate($this->quote); /* @phpstan-ignore-line */
    $this->rating->score = 5;
    $this->rating->save();

    loginApi($this->user);
});

it('can show', function () {
    loginApi($this->user);

    $responseData = getJson(route('api.ratings.show', [
        'rating' => $this->rating->id,
    ]))->assertOk()
        ->json('data');

    assertEquals($this->rating->id, $responseData['id']);
});

it('can include qualifier', function () {
    $responseData = getJson(route('api.ratings.show', [
        'rating' => $this->rating->getKey(),
        'include' => 'qualifier',
    ]))->json('data');

    expect($responseData['qualifier'])
        ->id->toEqual($this->user->getKey())
        ->name->toEqual($this->user->name)
        ->email->toEqual($this->user->email)
        ->created_at->toEqual($this->user->created_at);
});

it('can include rateable', function () {
    $responseData = getJson(route('api.ratings.show', [
        'rating' => $this->rating->getKey(),
        'include' => 'rateable',
    ]))->json('data');

    expect($responseData['rateable'])
        ->id->toEqual($this->quote->getKey())
        ->title->toEqual($this->quote->title)
        ->excerpt->toEqual($this->quote->excerpt)
        ->content->toEqual($this->quote->content)
        ->state->toEqual($this->quote->state)
        ->average_score->toEqual($this->quote->average_score)
        ->created_at->toEqual($this->quote->created_at)
        ->updated_at->toEqual($this->quote->updated_at);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.ratings.show', ['rating' => $this->rating->getKey()]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toBe('select `id`, `score`, `qualifier_id`, `qualifier_type`, `rateable_id`, `rateable_type`, `created_at`, `updated_at` from `ratings` where `id` = ? limit 1'),
        );

    DB::disableQueryLog();
});
