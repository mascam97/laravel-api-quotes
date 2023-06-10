<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->withState(Published::$name)->create();

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->withState(Published::$name)->create();

    loginApi($this->user);
});

it('can update', function () {
    putJson(route('api.quotes.update', ['quote' => $this->quote->id]), [
        'title' => 'new title',
        'content' => 'new content',
    ])->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->where('id', $this->quote->id)
                    ->where('title', 'new title')
                    ->where('content', 'new content')
                    ->has('average_rating')
                    ->has('state')
                    ->has('excerpt')
                    ->has('created_at')
                    ->has('updated_at');
            })->where('message', 'The quote was updated successfully')->etc();
        });
});

it('can show', function () {
    getJson(route('api.quotes.show', ['quote' => $this->quote->id]))
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->where('id', $this->quote->id)
                    ->has('title')
                    ->has('content')
                    ->has('average_rating')
                    ->has('state')
                    ->has('excerpt')
                    ->has('created_at')
                    ->has('updated_at');
            })->etc();
        });
});

it('cannot update data from not owner', function () {
    loginApi();

    putJson(route('api.quotes.update', ['quote' => $this->quote->id]), [
        'title' => 'new title not allowed',
        'content' => 'new content not allowed',
    ])->assertForbidden();

    assertDatabaseHas(Quote::class, [
        'id' => $this->quote->id,
        'title' => $this->quote->title,
        'content' => $this->quote->content,
    ]);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    putJson(route('api.quotes.update', ['quote' => $this->quote->id]), [
        'title' => 'new title',
        'content' => 'new content',
    ])->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select * from `quotes` where `id` = ? limit 1'),
            fn ($query) => $query->toBe('update `quotes` set `title` = ?, `content` = ?, `quotes`.`updated_at` = ? where `id` = ?'),
        );

    DB::disableQueryLog();
});
