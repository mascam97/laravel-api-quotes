<?php

namespace Tests\Feature;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/';

    public function test_view(): void
    {
        (new UserFactory)->setAmount(10)->create();
        (new QuoteFactory)->setAmount(20)->create();

        $this->get($this->url)
            ->assertOk()
            ->assertSee([count(User::all()), count(Quote::all())]);
    }
}
