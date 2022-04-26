<?php

namespace Tests\Feature\App\Api\Controllers\V1;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowUserControllerTest extends TestCase
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

    public function test_quotes_include(): void
    {
        Quote::factory(3)->create([
            'user_id' => $this->user->getKey(),
        ]);

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/{$this->user->getKey()}?include=quotes")
            ->json('data');

        $this->assertArrayNotHasKey('quotesCount', $responseData);
        $this->assertArrayHasKey('quotes', $responseData);
        $this->assertCount(3, $responseData['quotes']);
    }

    public function test_quotes_count_include(): void
    {
        Quote::factory(3)->create([
            'user_id' => $this->user->getKey(),
        ]);

        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/{$this->user->getKey()}?include=quotesCount")
            ->json('data');

        $this->assertArrayNotHasKey('quotes', $responseData);
        $this->assertArrayHasKey('quotesCount', $responseData);
        $this->assertEquals(3, $responseData['quotesCount']);
    }
}
