<?php

namespace Tests\Feature;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
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
            ->assertOk()
            ->assertSee([count(User::all()), count(Quote::all())]);
    }
}
