<?php

namespace Database\Factories;

use Domain\Gifts\Models\Gift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gift>
 */
class DBGiftFactory extends Factory
{
    protected $model = Gift::class;

    public function definition(): array
    {
        return [
            'note' => $this->faker->text,
            'amount' => $this->faker->numberBetween(1, 1000),
            'currency' => 'USD',
        ];
    }
}
