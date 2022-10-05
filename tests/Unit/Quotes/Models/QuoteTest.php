<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;

test('get excerpt', function () {
    $quote = new Quote();
    $quote->content = 'Sunt quaerat eveniet hic voluptatem quod quibusdam voluptas. Cum iusto assumenda mollitia ea ut consequuntur.';

    $this->assertEquals('Sunt quaerat eveniet hic voluptatem quod quibusdam voluptas. Cum iusto assu...', $quote->excerpt);
});

test('belongs to user', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create();

    $this->assertInstanceOf(User::class, $quote->user);
});
