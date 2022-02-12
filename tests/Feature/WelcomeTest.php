<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/';

    public function test_view()
    {
        User::factory(10)->create();
        Quote::factory(20)->create();

        $response = $this->get($this->url);

        $response->assertStatus(200)
        ->assertSee([count(User::all()), count(Quote::all())]);
    }
}
