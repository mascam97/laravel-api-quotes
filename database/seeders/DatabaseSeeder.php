<?php

namespace Database\Seeders;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        (new QuoteFactory)->setAmount(120)->create();

        $this->call(RatingUserQuoteSeeder::class);
    }
}
