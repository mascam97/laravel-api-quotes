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

it('requires data', function () {
    postJson(route('api.quotes.store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'title' => 'The title field is required.',
            'content' => 'The content field is required.',
        ]);
});

it('requires min characters in data', function () {
    postJson(route('api.quotes.store'), ['title' => 'a', 'content' => 'a'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'title' => 'The title must be at least 3 characters.',
            'content' => 'The content must be at least 3 characters.',
        ]);
});

it('validates unique title', function () {
    (new QuoteFactory)->withUser($this->user)->create(['title' => 'Unique title']);

    postJson(route('api.quotes.store'), ['title' => 'Unique title', 'content' => 'Some content'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'title' => 'The title has already been taken.',
        ]);
});
