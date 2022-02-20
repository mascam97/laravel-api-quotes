<?php

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuoteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $url = '/api/v2/quotes';

    private array $fillable = ['title', 'content'];

    private array $columns_collection = [
        'id', 'title', 'excerpt', 'author_name',
        'rating' => ['average', 'qualifiers'],
        'updated_ago',
    ];

    private array $columns = [
        'id', 'title', 'content',
        'author' => ['name', 'email'],
        'rating' => ['score_by_user', 'average', 'qualifiers'],
        'created_at', 'updated_at',
    ];

    private string $table = 'quotes';

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
            ->assertStatus(401);                // index
        $this->json('GET', "$this->url/{$this->quote->id}")
            ->assertStatus(401);     // show
        $this->json('POST', "$this->url", [])
            ->assertStatus(401);           // store
        $this->json('PUT', "$this->url/{$this->quote->id}", [])
            ->assertStatus(401); // update
        $this->json('DELETE', "$this->url/{$this->quote->id}")
        ->assertStatus(401);  // destroy
    }

    public function test_index(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', $this->url)
            ->assertJsonStructure([
                'data' => ['*' => $this->columns_collection],
            ])->assertStatus(200);
    }

    public function test_store_validate(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('POST', $this->url, [
                'title' => '',
                'content' => '',
            ])->assertJsonValidationErrors($this->fillable);
    }

    public function test_store(): void
    {
        $data = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->text(500),
        ];

        $this->actingAs($this->user, 'sanctum')
            ->json('POST', $this->url, $data)
            ->assertJsonMissingValidationErrors($this->fillable)
            ->assertSee('The quote was created successfully')
            ->assertJsonStructure(['data' => $this->columns])
            ->assertJson(['data' => $data])
            ->assertSee([$this->user->name, $this->user->email])
            ->assertStatus(201);

        $this->assertDatabaseHas($this->table, $data);
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
            ->json('GET', "$this->url/{$this->quote->id}")
            ->assertJsonStructure($this->columns)
            ->assertJson(['id' => $this->quote->id, 'content' => $this->quote->content])
            ->assertStatus(200);
    }

    public function test_update_validate(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('PUT', "$this->url/{$this->quote->id}", [
                'title' => '',
                'content' => '',
            ])->assertJsonValidationErrors($this->fillable);
    }

    public function test_update_policy(): void
    {
        /** @var User $userNotOwner */
        $userNotOwner = User::factory()->create();

        // just the owner $this->>user can delete his quote
        $this->actingAs($userNotOwner)
            ->put("$this->url/{$this->quote->id}", [
                'title' => 'new title not allowed',
                'content' => 'new content not allowed',
            ])->assertStatus(403);

        $this->assertDatabaseHas($this->table, [
            'title' => $this->quote->title,
            'content' => $this->quote->content,
        ]);
        $this->assertDatabaseMissing($this->table, [
            'title' => 'new title not allowed',
            'content' => 'new content not allowed',
        ]);
    }

    public function test_update(): void
    {
        $new_data = [
            'title' => 'new title',
            'content' => 'new content',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->json('PUT', "$this->url/{$this->quote->id}", $new_data);

        $response->assertJsonMissingValidationErrors($this->fillable)
            ->assertSee('The quote was updated successfully')
            ->assertJsonStructure(['data' => $this->columns])
            ->assertJson(['data' => $new_data])
            ->assertStatus(200);

        $this->assertDatabaseMissing($this->table, ['id' => $this->quote->id, 'title' => $this->quote->title]);
        $this->assertDatabaseHas($this->table, ['id' => $this->quote->id, 'title' => 'new title']);
    }

    public function test_destroy_policy(): void
    {
        /** @var User $userNotOwner */
        $userNotOwner = User::factory()->create();

        $this->actingAs($userNotOwner)
            ->delete("$this->url/{$this->quote->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas($this->table, [
            'title' => $this->quote->title,
            'content' => $this->quote->content,
        ]);
    }

    public function test_delete_404(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('DELETE', "$this->url/100")
            ->assertSee(null)->assertStatus(404);
    }

    public function test_delete(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('DELETE', "$this->url/{$this->quote->id}")
            ->assertSee('The quote was deleted successfully')->assertStatus(200);

        $this->assertDatabaseMissing($this->table, ['id' => $this->quote->id]);
    }

    public function test_rate_validate(): void
    {
        $this->actingAs($this->user, 'sanctum')->json(
            'POST',
            "$this->url/{$this->quote->id}/rate",
            ['score' => '']
        )->assertJsonValidationErrors('score');

        $this->actingAs($this->user, 'sanctum')->json(
            'POST',
            "$this->url/{$this->quote->id}/rate",
            ['score' => 'great quote']
        )->assertJsonValidationErrors('score');
    }

    public function test_rate_validate_range(): void
    {
        // Validate the range of score defined in config/rating.php
        $this->actingAs($this->user, 'sanctum')->json(
            'POST',
            "$this->url/{$this->quote->id}/rate",
            ['score' => '10']
        )->assertJsonValidationErrors('score')
            ->assertSee('The score must be between 1 and 5.');
    }

    public function test_rate(): void
    {
        $this->actingAs($this->user, 'sanctum')->json(
            'POST',
            "$this->url/{$this->quote->id}/rate",
            ['score' => 5]
        )->assertSee("The quote {$this->quote->id} was rated with 5 successfully")->assertStatus(200);

        $this->assertDatabaseHas('ratings', [
            'score' => 5,
            'rateable_type' => Quote::class,
            'rateable_id' => $this->quote->id,
            'qualifier_type' => User::class,
            'qualifier_id' => $this->user->id,
        ]);
    }

    public function test_rate_updated(): void
    {
        $this->user->rate($this->quote, 5);

        $this->actingAs($this->user, 'sanctum')->json(
            'POST',
            "$this->url/{$this->quote->id}/rate",
            ['score' => 1]
        )->assertSee("The quote {$this->quote->id} was rated with 1 successfully")->assertStatus(200);

        $this->assertDatabaseHas('ratings', [
            'score' => 1,
            'rateable_type' => Quote::class,
            'rateable_id' => $this->quote->id,
            'qualifier_type' => User::class,
            'qualifier_id' => $this->user->id,
        ]);
        $this->assertDatabaseMissing('ratings', [
            'score' => 5,
            'rateable_type' => Quote::class,
            'rateable_id' => $this->quote->id,
            'qualifier_type' => User::class,
            'qualifier_id' => $this->user->id,
        ]);
    }

    public function test_unrate(): void
    {
        // The user can unrate a quote with score = 0
        $this->user->rate($this->quote, 5);

        $this->actingAs($this->user, 'sanctum')->json(
            'POST',
            "$this->url/{$this->quote->id}/rate",
            ['score' => 0]
        )->assertSee("The quote {$this->quote->id} was unrated successfully")->assertStatus(200);

        $this->assertDatabaseMissing('ratings', [
            'score' => 5,
            'rateable_type' => Quote::class,
            'rateable_id' => $this->quote->id,
            'qualifier_type' => User::class,
            'qualifier_id' => $this->user->id,
        ]);
    }
}
