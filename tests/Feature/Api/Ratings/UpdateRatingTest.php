<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use function Pest\Laravel\putJson;

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

it('validates required data', function () {
    putJson(route('api.ratings.update', ['rating' => $this->rating->id]), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'score' => 'The score field is required.',
        ]);
});
