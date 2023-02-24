<?php

namespace Database\Seeders;

use Domain\Quotes\Models\Quote;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Database\Seeder;

class RatingUserQuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Exception
     */
    public function run(): void
    {
        // All the quotes have two rates
        for ($user_id = 1; $user_id <= 2; $user_id++) {
            for ($quote_id = 1; $quote_id <= 120; $quote_id++) {
                Rating::create([
                    'score' => random_int(1, 5),
                    'rateable_type' => Quote::class,
                    'rateable_id' => $quote_id,
                    'qualifier_type' => User::class,
                    'qualifier_id' => $user_id,
                ]);
            }
        }
    }
}
