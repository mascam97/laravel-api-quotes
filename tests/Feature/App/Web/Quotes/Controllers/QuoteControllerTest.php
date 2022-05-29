<?php

namespace Tests\Feature\App\Web\Quotes\Controllers;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/';

    public function test_view(): void
    {
        $user = (new UserFactory)->create();
        $quotes = (new QuoteFactory)->setAmount(3)->withUser($user)->create();

        $this->get($this->url)
            ->assertOk()
            ->assertSee($quotes->pluck('title')[0])
            ->assertSee($quotes->pluck('title')[1])
            ->assertSee($quotes->pluck('title')[2]);
    }
}
