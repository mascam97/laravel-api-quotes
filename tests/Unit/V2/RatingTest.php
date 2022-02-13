<?php

namespace Tests\Unit\V2;

use App\Models\Quote;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_rate_quotes()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user,
        ]);

        $user->rate($quote, 5);

        $this->assertInstanceOf(Collection::class, $user->ratings(Quote::class)->get());
        $this->assertInstanceOf(Collection::class, $quote->qualifiers(User::class)->get());
    }

    public function test_calculate_average_rating()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user,
        ]);

        $user->rate($quote, 5);
        $user2->rate($quote, 3);

        $this->assertEquals(4, $quote->averageRating(User::class));
    }

    public function test_rating_model()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create([
            'user_id' => $user->id,
        ]);

        $user->rate($quote, 5);

        $rating = Rating::first();

        $this->assertInstanceOf(Quote::class, $rating->rateable);
        $this->assertInstanceOf(User::class, $rating->qualifier);
    }
}
