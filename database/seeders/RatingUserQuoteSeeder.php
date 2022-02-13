<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;

class RatingUserQuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // All the quotes have two rates
        for ($user_id = 1; $user_id <= 2; $user_id++) {
            for ($quote_id = 1; $quote_id <= 120; $quote_id++) {
                Rating::create([
                    'score' => random_int(1, 5),
                    'rateable_type' => "App\Models\Quote",
                    'rateable_id' => $quote_id,
                    'qualifier_type' => "App\Models\User",
                    'qualifier_id' => $user_id,
                ]);
            }
        }
    }
}
