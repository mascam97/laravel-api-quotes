<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    loginApi($this->user);
});

it('validates required data', function () {
    postJson(route('api.ratings.store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'score' => 'The score field is required.',
            'rateableId' => 'The rateable id field is required.',
            'rateableType' => 'The rateable type field is required.',
        ]);
});
