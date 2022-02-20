<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/';

    public function test_view(): void
    {
        User::factory(10)->create();
        Quote::factory(20)->create();

        $this->get($this->url)
            ->assertStatus(200)
            ->assertSee([count(User::all()), count(Quote::all())]);
    }
}
