<?php

namespace Database\Factories;

use Domain\Quotes\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class DBQuoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        return [
            'user_id' => random_int(1, 10),
            'title' => $this->faker->sentence(nbWords: 3),
            'content' => $this->faker->text(),
            'average_score' => null,
        ];
    }
}
