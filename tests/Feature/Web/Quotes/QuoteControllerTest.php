<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\get;

beforeEach(function () {
    $user = User::factory()->create();
    $this->quotes = (new QuoteFactory)->setAmount(3)->withUser($user)->create();
});

it('can see the main view', function () {
    get(route('web.welcome'))
        ->assertOk()
        ->assertSee($this->quotes->pluck('title')[0])
        ->assertSee($this->quotes->pluck('title')[1])
        ->assertSee($this->quotes->pluck('title')[2]);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    get(route('web.welcome'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toBe('select `title`, `content`, `created_at` from `quotes`'),
        );

    DB::disableQueryLog();
});
