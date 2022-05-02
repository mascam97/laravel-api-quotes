<?php

namespace Database\Seeders;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        (new UserFactory)->create();
        (new QuoteFactory)->setAmount(120)->create();

        $this->call(RatingUserQuoteSeeder::class);
    }
}
