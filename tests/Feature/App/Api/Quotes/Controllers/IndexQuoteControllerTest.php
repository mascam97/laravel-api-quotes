<?php

namespace Tests\Feature\App\Api\Quotes\Controllers;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexQuoteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $url = '/api/v1/quotes';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new UserFactory)->create();

        (new QuoteFactory)->setAmount(5)->withUser($this->user)->create();
    }

    public function test_title_filter(): void
    {
        $quote = (new QuoteFactory)->withUser($this->user)->create([
            'title' => 'Hamlet',
        ]);

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[title]=hamlet")
            ->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($quote->getKey(), $responseData[0]['id']);
    }

    public function test_content_filter(): void
    {
        $quote = (new QuoteFactory)->withUser($this->user)->create([
            'content' => 'Some text about something',
        ]);

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[content]=Some text about something")
            ->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($quote->getKey(), $responseData[0]['id']);
    }

    public function test_user_id_filter(): void
    {
        /** @var User $newUser */
        $newUser = (new UserFactory)->create();

        $quote = (new QuoteFactory)->withUser($newUser)->create();

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[user_id]=$newUser->id")
            ->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($quote->getKey(), $responseData[0]['id']);
    }

    public function test_user_include(): void
    {
        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?include=user")
            ->json('data');

        $this->assertCount(5, $responseData);
        $this->assertArrayHasKey('user', $responseData[0]);

        $newUser = (new UserFactory)->create();
        $quote = (new QuoteFactory)->withUser($newUser)->create([
            'title' => 'Some text about something',
        ]);

        $responseDataTwo = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[title]=Some text about something&include=user")
            ->json('data');

        $this->assertCount(1, $responseDataTwo);
        $this->assertEquals($quote->getKey(), $responseDataTwo[0]['id']);
        $this->assertEquals($newUser->getKey(), $responseDataTwo[0]['user']['id']);
    }

    public function test_id_sort(): void
    {
        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?sort=id")
            ->json('data');

        $this->assertEquals(1, $responseData[0]['id']);

        $responseDataTwo = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?sort=-id")
            ->json('data');

        $this->assertEquals(5, $responseDataTwo[0]['id']);
    }

    public function test_title_sort(): void
    {
        (new QuoteFactory)->withUser($this->user)->create([
            'title' => 'AAA',
        ]);

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?sort=title")
            ->json('data');

        $this->assertEquals('AAA', $responseData[0]['title']);

        $responseDataTwo = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?sort=-title")
            ->json('data');

        $this->assertEquals('AAA', $responseDataTwo[5]['title']);
    }
}
