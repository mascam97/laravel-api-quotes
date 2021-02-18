<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    private $url = "/api/v1/users";
    private $columns_collection = ['id', 'title', 'excerpt', 'created_ago', 'updated_ago'];
    private $columns = ['id', 'name', 'email', 'quotes_count', 'created_ago'];
    private $table = 'users';

    public function test_guest_unauthorized()
    {
        $user = User::factory()->create();

        $this->json("GET", "$this->url")->assertStatus(401);                  // index
        $this->json("GET", "$this->url/$user->id")->assertStatus(401);        // show
        $this->json("GET", "$this->url/$user->id/quotes")->assertStatus(401); // index quotes
    }

    public function test_index()
    {
        $user = User::factory()->create();

        Quote::factory()->create([
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user, 'sanctum')->json('GET', $this->url);

        $response->assertJsonStructure([
            'data' => ['*' => $this->columns]
        ])->assertStatus(200);
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

        $response = $this->actingAs($user, 'sanctum')->json('GET', "$this->url/$user->id");

        $response->assertSee([$user->id, $user->name])
            ->assertStatus(200);
    }

    public function test_index_quotes()
    {
        $user = User::factory()->create();

        Quote::factory()->create([
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user, 'sanctum')->json('GET', "api/v1/users/$user->id/quotes");

        $response->assertJsonStructure([
            'data' => ['*' => $this->columns_collection]
        ])->assertStatus(200);
    }
}
