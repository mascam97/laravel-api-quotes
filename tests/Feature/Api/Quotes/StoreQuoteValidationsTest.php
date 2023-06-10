<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->withState(Published::$name)->create();

    loginApi($this->user);
});

it('can store', function () {
    postJson(route('api.quotes.store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'title' => 'The title field is required.',
            'content' => 'The content field is required.',
        ]);
});
