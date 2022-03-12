<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/users';

    private array $fields = ['id', 'name', 'email', 'created_at'];

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
            ->assertUnauthorized();                  // index
        $this->json('GET', "$this->url/{$this->user->id}")
            ->assertUnauthorized();        // show
    }

    public function test_index(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', $this->url)
            ->assertJsonStructure([
                'data' => ['*' => $this->fields],
            ])->assertOk();
    }

    public function test_show_404(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/100000")
            ->assertNotFound();
    }

    public function test_show(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/{$this->user->id}")
        ->assertSee([$this->user->id, $this->user->name])
            ->assertOk();
    }
}
