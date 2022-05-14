<?php

namespace Tests\Feature\App\Api\Users\Controllers;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexUserControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/users';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new UserFactory)->create();

        (new UserFactory)->setAmount(4)->create();
    }

    public function test_id_filter(): void
    {
        /** @var User $newUser */
        $newUser = (new UserFactory)->create();

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[id]=$newUser->id")
            ->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($newUser->getKey(), $responseData[0]['id']);
    }

    public function test_name_filter(): void
    {
        $newUser = (new UserFactory)->create([
            'name' => 'Shakespeare',
        ]);

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[name]=shakespeare")
            ->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($newUser->getKey(), $responseData[0]['id']);
    }

    public function test_quotes_include(): void
    {
        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?include=quotes")
            ->json('data');

        $this->assertCount(5, $responseData);
        $this->assertArrayHasKey('quotes', $responseData[0]);

        $newUser = (new UserFactory)->create([
            'name' => 'User with quote',
        ]);
        $quote = (new QuoteFactory)->withUser($newUser)->create();

        $responseDataTwo = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[name]=User with quote&include=quotes")
            ->json('data');

        $this->assertCount(1, $responseDataTwo);
        $this->assertCount(1, $responseDataTwo[0]['quotes']);
        $this->assertEquals($quote->getKey(), $responseDataTwo[0]['quotes'][0]['id']);
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

    public function test_name_sort(): void
    {
        $this->user->name = 'AAA';
        $this->user->update();

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?sort=name")
            ->json('data');

        $this->assertEquals('AAA', $responseData[0]['name']);

        $responseDataTwo = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?sort=-name")
            ->json('data');

        $this->assertEquals('AAA', $responseDataTwo[4]['name']);
    }
}
