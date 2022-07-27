<?php

namespace Tests\Unit\Rating;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Exceptions\InvalidScore;
use Domain\Rating\Models\Rating;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Quote $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */
    }

    /**
     * @throws InvalidScore
     */
    public function test_users_rate_quotes(): void
    {
        $this->user->rate($this->quote, 5);

        $this->assertInstanceOf(Collection::class, $this->user->ratings(Quote::class)->get());
        $this->assertInstanceOf(Collection::class, $this->quote->qualifiers(User::class)->get());
    }

    /**
     * @throws InvalidScore
     */
    public function test_calculate_average_rating(): void
    {
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        $this->user->rate($this->quote, 5);
        $anotherUser->rate($this->quote, 3);

        $this->assertEquals(4, $this->quote->averageRating(User::class));
    }

    /**
     * @throws InvalidScore
     */
    public function test_rating_model(): void
    {
        $this->user->rate($this->quote, 5);

        $rating = Rating::query()->first();

        $this->assertInstanceOf(Quote::class, $rating->rateable);
        $this->assertInstanceOf(User::class, $rating->qualifier);
    }
}
