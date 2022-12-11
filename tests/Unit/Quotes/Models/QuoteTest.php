<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Banned;
use Domain\Quotes\States\Drafted;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;
use Spatie\ModelStates\Exceptions\CouldNotPerformTransition;

it('can get excerpt', function () {
    $quote = new Quote();
    $quote->content = 'Sunt quaerat eveniet hic voluptatem quod quibusdam voluptas. Cum iusto assumenda mollitia ea ut consequuntur.';

    assertEquals('Sunt quaerat eveniet hic voluptatem quod quibusdam voluptas. Cum iusto assu...', $quote->excerpt);
});

it('belongs to user') /* @phpstan-ignore-line */
    ->expect(fn () => (new QuoteFactory)->withUser(User::factory()->create())->create())
    ->user->toBeInstanceOf(User::class);

it('can get by id', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create();

    $filteredQuotes = Quote::query()->whereId($quote->id)->get();
    /** @var Quote $filteredQuote */
    $filteredQuote = $filteredQuotes[0];

    expect($filteredQuotes)->toHaveCount(1);
    expect($filteredQuote->getKey())->toBe($quote->getKey());
});

it('is created with draft state', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create();

    assertTrue($quote->state->equals(Drafted::class));
});

it('can transition from draft to published', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create();

    $quote->state->transitionTo(Published::class);

    assertTrue($quote->state->equals(Published::class));
});

it('can transition from published to banned', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create([
        'state' => Published::class,
    ]);

    $quote->state->transitionTo(Banned::class);

    assertTrue($quote->state->equals(Banned::class));
});

it('cannot transition from draft to banned', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create();

    $quote->state->transitionTo(Banned::class);
})->throws(CouldNotPerformTransition::class);

it('cannot transition from published to draft', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create([
        'state' => Published::class,
    ]);

    $quote->state->transitionTo(Drafted::class);
})->throws(CouldNotPerformTransition::class);

it('cannot transition from banned to published', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create([
        'state' => Banned::class,
    ]);

    $quote->state->transitionTo(Published::class);
})->throws(CouldNotPerformTransition::class);
