<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuoteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    private $url = "/api/v1/quotes";
    private $fillable = ['title', 'content'];
    private $columns = ['id', 'title', 'content', 'created_at', 'updated_at'];
    private $table = 'quotes';

    public function test_index()
    {
        Quote::factory(10)->create();

        $response = $this->json('GET', $this->url);

        $response->assertJsonStructure([
            'data' => [
                '*' => $this->columns
            ]
        ])->assertStatus(200);
    }

    public function test_validate_store()
    {
        $response = $this->json('POST', $this->url, [
            'title' => '',
            'content' => ''
        ]);
        $response->assertJsonValidationErrors($this->fillable);
    }

    public function test_store()
    {
        $data = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->text(500)
        ];

        $response = $this->json('POST', $this->url, $data);

        $response->assertJsonMissingValidationErrors($this->fillable)
            ->assertJsonStructure($this->columns)
            ->assertJson($data)
            ->assertStatus(201);
        $this->assertDatabaseHas($this->table, $data);
    }

    public function test_404_show()
    {
        $response = $this->json('GET', "$this->url/100000");

        $response->assertStatus(404);
    }

    public function test_show()
    {
        $quote = Quote::factory()->create();

        $response = $this->json('GET', "$this->url/$quote->id");

        $response->assertJsonStructure($this->columns)
            ->assertJson(['id' => $quote->id, 'content' => $quote->content])
            ->assertStatus(200);
    }

    public function test_validate_update()
    {
        $quote = Quote::factory()->create();

        $response = $this->json('PUT', "$this->url/$quote->id", [
            'title' => '',
            'content' => ''
        ]);

        $response->assertJsonValidationErrors($this->fillable);
    }

    public function test_update()
    {
        $quote = Quote::factory()->create();
        $new_data = [
            'title' => 'new title',
            'content' => 'new content'
        ];

        $response = $this->json('PUT', "$this->url/$quote->id", $new_data);

        $response->assertJsonMissingValidationErrors($this->fillable)
            ->assertJsonStructure($this->columns)
            ->assertJson($new_data)
            ->assertStatus(200);
        $this->assertDatabaseMissing($this->table, ['id' => $quote->id, 'title' => $quote->title]);
        $this->assertDatabaseHas($this->table, ['id' => $quote->id, 'title' => 'new title']);
    }

    public function test_delete()
    {
        $quote = Quote::factory()->create();

        $response = $this->json('DELETE', "$this->url/$quote->id");

        $response->assertSee(null)->assertStatus(204);
        $this->assertDatabaseMissing($this->table, ['id' => $quote->id]);
    }
}
