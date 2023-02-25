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
        $this->call(PermissionsAndRolesSeeder::class);

        /** @var User $admin */
        $admin = User::factory()->create(['name' => 'admin']);
        $admin->assignRole('Administrator');

        User::factory(9)->create();
        (new QuoteFactory)->setAmount(120)->create();

        $this->call(RatingUserQuoteSeeder::class);
    }
}
