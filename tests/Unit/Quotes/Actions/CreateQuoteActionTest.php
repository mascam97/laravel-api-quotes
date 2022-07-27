<?php

namespace Tests\Unit\Quotes\Actions;

use Domain\Quotes\Actions\CreateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\States\Drafted;
use Domain\Quotes\States\Published;
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

        $this->user = User::factory()->create();
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
        $this->assertEquals(Drafted::class, $quote->state);
    }

    public function test_quote_can_be_published(): void
    {
        $quoteData = new QuoteData(
            title: 'Title', content: 'Content', published: true
        );

        $createQuoteAction = new CreateQuoteAction();

        $quote = $createQuoteAction->__invoke($quoteData, $this->user);

        $this->assertTrue($quote->user()->is($this->user));
        $this->assertEquals($quote->title, $quoteData->title);
        $this->assertEquals($quote->content, $quoteData->content);
        $this->assertEquals(Published::class, $quote->state);
    }
}
