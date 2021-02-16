<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    private $columns_collection = ['id', 'title', 'excerpt', 'created_ago', 'updated_ago'];
    private $table = 'quotes';

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
