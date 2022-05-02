<?php

namespace Tests\Unit\Support;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Support\Models\Rating;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Quote $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new UserFactory)->create();
        $this->quote = (new QuoteFactory)->withUser($this->user)->create();
    }

    public function test_users_rate_quotes()
    {
        $this->user->rate($this->quote, 5);

        $this->assertInstanceOf(Collection::class, $this->user->ratings(Quote::class)->get());
        $this->assertInstanceOf(Collection::class, $this->quote->qualifiers(User::class)->get());
    }

    public function test_calculate_average_rating()
    {
        /** @var User $anotherUser */
        $anotherUser = (new UserFactory)->create();

        $this->user->rate($this->quote, 5);
        $anotherUser->rate($this->quote, 3);

        $this->assertEquals(4, $this->quote->averageRating(User::class));
    }

    public function test_rating_model()
    {
        $this->user->rate($this->quote, 5);

        $rating = Rating::first();

        $this->assertInstanceOf(Quote::class, $rating->rateable);
        $this->assertInstanceOf(User::class, $rating->qualifier);
    }
}
