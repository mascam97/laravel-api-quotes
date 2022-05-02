<?php

namespace Tests\Unit\Quotes\Actions;

use Domain\Quotes\Actions\CreateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateQuoteActionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new UserFactory)->create();
    }

    public function test_quote_is_created(): void
    {
        $quoteData = new QuoteData(
            title: 'Title', content: 'Content'
        );
        $createQuoteAction = new CreateQuoteAction();

        $quote = $createQuoteAction->__invoke($quoteData, $this->user);

        $this->assertTrue($quote->user()->is($this->user));
        $this->assertEquals($quote->title, $quoteData->title);
        $this->assertEquals($quote->content, $quoteData->content);
    }
}
