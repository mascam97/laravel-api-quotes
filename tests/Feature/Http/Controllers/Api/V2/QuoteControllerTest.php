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
    private $url = "/api/v2/quotes";
    private $fillable = ['title', 'content'];
    private $columns_collection = [
        'id', 'title', 'excerpt', 'author_name',
        'rating' => ['average', 'qualifiers'],
        'updated_ago'
    ];
    private $columns = [
        'id', 'title', 'content',
        'author' => ['name', 'email'],
        'rating' => ['score_by_user', 'average', 'qualifiers'],
        'created_at', 'updated_at'
    ];
    private $table = 'quotes';

    public function test_guest_unauthorized()
    {
        $quote = Quote::factory()->create([
            'user_id' => User::factory()->create()
        ]);

        $this->json("GET", "$this->url")->assertStatus(401);                // index
        $this->json("GET", "$this->url/$quote->id")->assertStatus(401);     // show
        $this->json("POST", "$this->url", [])->assertStatus(401);           // store
        $this->json("PUT", "$this->url/$quote->id", [])->assertStatus(401); // update
        $this->json("DELETE", "$this->url/$quote->id")->assertStatus(401);  // destroy
    }

    public function test_index()
    {
        $user = User::factory()->create();

        Quote::factory()->create([
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user, 'sanctum')->json('GET', $this->url);

        $response->assertJsonStructure([
            'data' => ['*' => $this->columns_collection]
        ])->assertStatus(200);
    }

    public function test_store_validate()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->json('POST', $this->url, [
            'title' => '',
            'content' => ''
        ]);
        $response->assertJsonValidationErrors($this->fillable);
    }

    public function test_store()
    {
        $user = User::factory()->create();
        $data = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->text(500)
        ];

        $response = $this->actingAs($user, 'sanctum')->json('POST', $this->url, $data);

        $response->assertJsonMissingValidationErrors($this->fillable)
            ->assertSee('The quote was created successfully')
            ->assertJsonStructure(['data' => $this->columns])
            ->assertJson(['data' => $data])
            ->assertSee([$user->name, $user->email])
            ->assertStatus(201);
        $this->assertDatabaseHas($this->table, $data);
    }

    public function test_show_404()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->json('GET', "$this->url/100000");

        $response->assertStatus(404);
    }

    public function test_show()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'sanctum')->json('GET', "$this->url/$quote->id");

        $response->assertJsonStructure($this->columns)
            ->assertJson(['id' => $quote->id, 'content' => $quote->content])
            ->assertStatus(200);
    }

    public function test_update_validate()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'sanctum')->json('PUT', "$this->url/$quote->id", [
            'title' => '',
            'content' => ''
        ]);

        $response->assertJsonValidationErrors($this->fillable);
    }

    public function test_update_policy()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);

        $user_malicious = User::factory()->create();
        // just the owner $user can delete his quote
        $response = $this->actingAs($user_malicious)->put("$this->url/$quote->id", [
            'title' => 'new title not allowed',
            'content' => 'new content not allowed'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas($this->table, [
            'title' => $quote->title,
            'content' => $quote->content
        ]);
        $this->assertDatabaseMissing($this->table, [
            'title' => 'new title not allowed',
            'content' => 'new content not allowed'
        ]);
    }

    public function test_update()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);
        $new_data = [
            'title' => 'new title',
            'content' => 'new content'
        ];

        $response = $this->actingAs($user, 'sanctum')->json('PUT', "$this->url/$quote->id", $new_data);

        $response->assertJsonMissingValidationErrors($this->fillable)
            ->assertSee('The quote was updated successfully')
            ->assertJsonStructure(['data' => $this->columns])
            ->assertJson(['data' => $new_data])
            ->assertStatus(200);
        $this->assertDatabaseMissing($this->table, ['id' => $quote->id, 'title' => $quote->title]);
        $this->assertDatabaseHas($this->table, ['id' => $quote->id, 'title' => 'new title']);
    }

    public function test_destroy_policy()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);

        $user_malicious = User::factory()->create();
        $response = $this->actingAs($user_malicious)->delete("$this->url/$quote->id");

        $response->assertStatus(403);
        $this->assertDatabaseHas($this->table, [
            'title' => $quote->title,
            'content' => $quote->content
        ]);
    }

    public function test_delete_404()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->json('DELETE', "$this->url/1");

        $response->assertSee(null)->assertStatus(404);
    }

    public function test_delete()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'sanctum')->json('DELETE', "$this->url/$quote->id");

        $response->assertSee('The quote was deleted successfully')->assertStatus(200);
        $this->assertDatabaseMissing($this->table, ['id' => $quote->id]);
    }

    public function test_rate_validate()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'sanctum')->json(

            'POST',
            "$this->url/$quote->id/rate",
            ['score' => '']
        );
        $response->assertJsonValidationErrors('score');

        $response_not_a_number = $this->actingAs($user, 'sanctum')->json(
            'POST',
            "$this->url/$quote->id/rate",
            ['score' => 'great quote']
        );
        $response_not_a_number->assertJsonValidationErrors('score');
    }

    public function test_rate_validate_range()
    {
        // Validate the range of score defined in config/rating.php
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'sanctum')->json(
            'POST',
            "$this->url/$quote->id/rate",
            ['score' => '10']
        );
        $response->assertJsonValidationErrors('score')
            ->assertSee("The score must be between 1 and 5.");
    }

    public function test_rate()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'sanctum')->json(
            'POST',
            "$this->url/$quote->id/rate",
            ['score' => 5]
        );

        $response->assertSee("The quote $quote->id was rated with 5 successfully")->assertStatus(200);
        $this->assertDatabaseHas('ratings', [
            'score' => 5,
            "rateable_type" => "App\Models\Quote",
            "rateable_id" => $quote->id,
            "qualifier_type" => "App\Models\User",
            'qualifier_id' => $user->id,
        ]);
    }

    public function test_rate_updated()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);
        $user->rate($quote, 5);

        $response = $this->actingAs($user, 'sanctum')->json(
            'POST',
            "$this->url/$quote->id/rate",
            ['score' => 1]
        );

        $response->assertSee("The quote $quote->id was rated with 1 successfully")->assertStatus(200);
        $this->assertDatabaseHas('ratings', [
            'score' => 1,
            "rateable_type" => "App\Models\Quote",
            "rateable_id" => $quote->id,
            "qualifier_type" => "App\Models\User",
            'qualifier_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('ratings', [
            'score' => 5,
            "rateable_type" => "App\Models\Quote",
            "rateable_id" => $quote->id,
            "qualifier_type" => "App\Models\User",
            'qualifier_id' => $user->id,
        ]);
    }

    public function test_unrate()
    {
        // The user can unrate a quote with score = 0
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id
        ]);
        $user->rate($quote, 5);

        $response = $this->actingAs($user, 'sanctum')->json(
            'POST',
            "$this->url/$quote->id/rate",
            ['score' => 0]
        );

        $response->assertSee("The quote $quote->id was unrated successfully")->assertStatus(200);
        $this->assertDatabaseMissing('ratings', [
            'score' => 5,
            "rateable_type" => "App\Models\Quote",
            "rateable_id" => $quote->id,
            "qualifier_type" => "App\Models\User",
            'qualifier_id' => $user->id,
        ]);
    }
}
