<?php

namespace Tests\Feature\App\Api\Quotes\Controllers;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowQuoteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $url = '/api/v1/quotes';

    private User $user;

    private Quote $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

        (new QuoteFactory)->setAmount(3)->withUser($this->user)->create();
    }

    public function test_user_include(): void
    {
        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/{$this->quote->getKey()}?include=user")
            ->json('data');

        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals($this->user->getKey(), $responseData['user']['id']);
    }
}
