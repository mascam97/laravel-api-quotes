<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->withState(Published::$name)->create();

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->withState(Published::$name)->create();

    login($this->user);
});

it('can show', function () {
    $responseData = getJson(route('api.quotes.show', ['quote' => $this->quote->id]))
        ->assertJsonStructure([
            'data' => ['id', 'title', 'content', 'state', 'average_rating', 'excerpt', 'created_at', 'updated_at'],
        ])->assertOk()
        ->json('data');

    expect($responseData)
        ->id->toBe($this->quote->id)
        ->content->toBe($this->quote->content);
});

it('can include user', function () {
    $responseData = getJson(route('api.quotes.show', [
        'quote' => $this->quote->getKey(),
        'include' => 'user',
    ]))->json('data');

    assertArrayHasKey('user', $responseData);
    assertEquals($this->user->getKey(), $responseData['user']['id']);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.quotes.show', ['quote' => $this->quote->getKey()]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toBe('select `id`, `title`, `content`, `state`, `average_score`, `user_id`, `created_at`, `updated_at` from `quotes` where `state` = ? and `id` = ? limit 1'),
        );

    DB::disableQueryLog();
});
