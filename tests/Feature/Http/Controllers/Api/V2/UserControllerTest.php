<?php

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v2/users';

    private array $columns_collection = [
        'id', 'title', 'excerpt',
        'rating' => ['average', 'qualifiers'],
        'created_ago', 'updated_ago',
    ];

    private array $columns = ['id', 'name', 'email', 'quotes_count', 'ratings_count', 'created_ago'];

    private string $table = 'users';

    private User $user;

    private Quote $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->quote = Quote::factory()->create([
            'user_id' => $this->user,
        ]);
    }

    public function test_guest_unauthorized(): void
    {
        $this->json('GET', "$this->url")
            ->assertStatus(401);                  // index
        $this->json('GET', "$this->url/{$this->user->id}")
            ->assertStatus(401);        // show
        $this->json('GET', "$this->url/{$this->user->id}/quotes")
            ->assertStatus(401); // index quotes
    }

    public function test_index(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', $this->url)
        ->assertJsonStructure([
            'data' => ['*' => $this->columns],
        ])->assertStatus(200);
    }

    public function test_show_404(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/100000")
            ->assertStatus(404);
    }

    public function test_show(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/{$this->user->id}")
            ->assertSee([$this->user->id, $this->user->name])
            ->assertStatus(200);
    }

    public function test_index_quotes(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/{$this->user->id}/quotes")
            ->assertJsonStructure([
                'data' => ['*' => $this->columns_collection],
            ])->assertStatus(200);
    }
}
