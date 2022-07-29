<?php

namespace Tests\Feature\App\Api\Quotes\Controllers;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuoteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $url = '/api/v1/quotes';

    private array $fillable = ['title', 'content'];

    private array $fields = ['id', 'title', 'content', 'state', 'excerpt', 'created_at', 'updated_at'];

    private string $table = 'quotes';

    private User $user;

    private Quote $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->quote = (new QuoteFactory)->withUser($this->user)->create(); /* @phpstan-ignore-line */
    }

    public function test_guest_unauthorized(): void
    {
        $this->json('GET', "$this->url")
            ->assertUnauthorized();                // index
        $this->json('GET', "$this->url/{$this->quote->id}")
            ->assertUnauthorized();     // show
        $this->json('POST', "$this->url", [])
            ->assertUnauthorized();           // store
        $this->json('PUT', "$this->url/{$this->quote->id}", [])
            ->assertUnauthorized(); // update
        $this->json('DELETE', "$this->url/{$this->quote->id}")
            ->assertUnauthorized();  // destroy
    }

    public function test_index(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', $this->url)
            ->assertJsonStructure([
                'data' => ['*' => $this->fields],
            ])->assertOk();
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
            ->assertJsonStructure(['data' => $this->fields])
            ->assertJson(['data' => $data])
            ->assertSee([$this->user->name, $this->user->email])
            ->assertCreated();

        $this->assertDatabaseHas($this->table, $data);
    }

    public function test_show_404(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/100000")
            ->assertNotFound();
    }

    public function test_show(): void
    {
        $responseData = $this->actingAs($this->user, 'sanctum')
            ->json('GET', "$this->url/{$this->quote->id}")
            ->assertJsonStructure(['data' => $this->fields])
            ->assertOk()
            ->json('data');

        $this->assertEquals($this->quote->id, $responseData['id']);
        $this->assertEquals($this->quote->content, $responseData['content']);
    }

    public function test_update_policy(): void
    {
        /** @var User $userNotOwner */
        $userNotOwner = User::factory()->create();
        // just the owner $this->user can delete his quote

        $this->actingAs($userNotOwner)
            ->put("$this->url/{$this->quote->id}", [
                'title' => 'new title not allowed',
                'content' => 'new content not allowed',
            ])->assertForbidden();

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

        $this->actingAs($this->user, 'sanctum')
            ->json('PUT', "$this->url/{$this->quote->id}", $new_data)
            ->assertJsonMissingValidationErrors($this->fillable)
            ->assertSee('The quote was updated successfully')
            ->assertJsonStructure(['data' => $this->fields])
            ->assertJson(['data' => $new_data])
            ->assertOk();

        $this->assertDatabaseMissing($this->table, ['id' => $this->quote->id, 'title' => $this->quote->title]);
        $this->assertDatabaseHas($this->table, ['id' => $this->quote->id, 'title' => 'new title']);
    }

    public function test_destroy_policy(): void
    {
        /** @var User $UserNotOwner */
        $UserNotOwner = User::factory()->create();

        $this->actingAs($UserNotOwner)
            ->delete("$this->url/{$this->quote->id}")
            ->assertForbidden();

        $this->assertDatabaseHas($this->table, [
            'title' => $this->quote->title,
            'content' => $this->quote->content,
        ]);
    }

    public function test_delete_404(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('DELETE', "$this->url/1000")
            ->assertSee([])->assertNotFound();
    }

    public function test_delete(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->json('DELETE', "$this->url/{$this->quote->id}")
            ->assertSee('The quote was deleted successfully')->assertOk();

        $this->assertDatabaseMissing($this->table, ['id' => $this->quote->id]);
    }
}
