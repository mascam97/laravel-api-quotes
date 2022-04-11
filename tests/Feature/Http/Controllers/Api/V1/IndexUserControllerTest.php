<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Domain\Quotes\Models\Quote;
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

        $this->user = User::factory()->create();

        User::factory(4)->create();
    }

    public function test_id_filter(): void
    {
        /** @var User $newUser */
        $newUser = User::factory()->create();

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url?filter[id]=$newUser->id")
            ->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($newUser->getKey(), $responseData[0]['id']);
    }

    public function test_name_filter(): void
    {
        $newUser = User::factory()->create([
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

        $newUser = User::factory()->create([
            'name' => 'User with quote',
        ]);
        $quote = Quote::factory()->create([
            'user_id'=> $newUser->getKey(),
        ]);

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
