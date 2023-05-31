<?php

namespace Database\Seeders;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionsAndRolesSeeder::class);

        $admin = new User();
        $admin->name = config('admin.name');
        $admin->email = config('admin.email');
        $admin->password = Hash::make(config('admin.password'));
        $admin->locale = App::getLocale();
        $admin->save();

        $admin->assignRole('Administrator');

        if (! App::environment('production')) {
            $this->call(PassportClientsSeeder::class);

            User::factory(9)->create();

            /** @var Collection $usersId */
            $usersId = User::query()
                ->whereNot('id', $admin->getKey())
                ->select('id')
                ->get()
                ->pluck('id');

            (new QuoteFactory)->setAmount(120)->create(['user_id' => $usersId->random()]);

            $this->call(RatingUserQuoteSeeder::class);
        }
    }
}
