<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->withState(Published::$name)->create();

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->withState(Published::$name)->create();

    loginApi($this->user);
});

it('can delete', function () {
    deleteJson(route('api.quotes.destroy', ['quote' => $this->quote->id]))
        ->assertOk()
        ->assertJson(['message' => 'The quote was deleted successfully']);

    assertDatabaseMissing(Quote::class, ['id' => $this->quote->id]);
});

it('cannot destroy data from not owner', function () {
    loginApi();

    deleteJson(route('api.quotes.destroy', ['quote' => $this->quote->id]))
        ->assertForbidden();

    assertDatabaseHas(Quote::class, [
        'id' => $this->quote->id,
        'title' => $this->quote->title,
        'content' => $this->quote->content,
    ]);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    deleteJson(route('api.quotes.destroy', ['quote' => $this->quote->id]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select * from `quotes` where `id` = ? limit 1'),
            fn ($query) => $query->toBe('delete from `quotes` where `id` = ?'),
        );

    DB::disableQueryLog();
});
