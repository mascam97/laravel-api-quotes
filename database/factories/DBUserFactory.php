<?php

namespace Database\Factories;

use Domain\Users\Enums\SexEnum;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class DBUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'sex' => SexEnum::NOT_APPLICABLE,
            'email_verified_at' => now(),
            'email_subscribed_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'locale' => 'en_US',
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): self
    {
        return $this->state(function () {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function notEmailSubscribed(): self
    {
        return $this->state(function () {
            return [
                'email_subscribed_at' => null,
            ];
        });
    }

    public function deleted(): self
    {
        return $this->state(function () {
            return [
                'deleted_at' => now(),
            ];
        });
    }
}
