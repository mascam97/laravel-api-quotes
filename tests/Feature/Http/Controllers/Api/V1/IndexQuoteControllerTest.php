<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Quote;
use App\Models\User;
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

        $this->user = User::factory()->create();

        Quote::factory(5)->create([
            'user_id' => $this->user->getKey(),
        ]);
    }

    public function test_title_filter(): void
    {
        $quote = Quote::factory()->create([
            'title' => 'Hamlet',
            'user_id' => $this->user,
        ]);

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[title]=hamlet")
            ->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($quote->getKey(), $responseData[0]['id']);
    }

    public function test_content_filter(): void
    {
        $quote = Quote::factory()->create([
            'content' => 'Some text about something',
            'user_id' => $this->user,
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
        $newUser = User::factory()->create();

        $quote = Quote::factory()->create([
            'user_id' => $newUser,
        ]);

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

        $newUser = User::factory()->create();
        $quote = Quote::factory()->create([
            'title' => 'Some text about something',
            'user_id'=> $newUser->getKey(),
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
        Quote::factory()->create([
            'title' => 'AAA',
            'user_id' => $this->user->getKey(),
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
