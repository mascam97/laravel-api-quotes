<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->create();

    login($this->user);
});

it('can show', function () {
    $responseData = getJson(route('quotes.show', ['quote' => $this->quote->id]))
        ->assertJsonStructure([
            'data' => ['id', 'title', 'content', 'state', 'average_rating', 'excerpt', 'created_at', 'updated_at'],
        ])->assertOk()
        ->json('data');

    expect($responseData)
        ->id->toBe($this->quote->id)
        ->content->toBe($this->quote->content);
});

it('can include user', function () {
    $responseData = getJson(route('quotes.show', [
        'quote' => $this->quote->getKey(),
        'include' => 'user',
    ]))->json('data');

    assertArrayHasKey('user', $responseData);
    assertEquals($this->user->getKey(), $responseData['user']['id']);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('quotes.show', ['quote' => $this->quote->getKey()]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select * from `quotes` where `id` = ? limit 1'),
            fn ($query) => $query->toBe('select avg(`score`) as aggregate from `users` inner join `ratings` on `users`.`id` = `ratings`.`qualifier_id` where `ratings`.`rateable_id` = ? and `ratings`.`rateable_type` = ? and `ratings`.`qualifier_type` = ? and `ratings`.`rateable_type` = ?'),
        );

    DB::disableQueryLog();
});
