<?php

namespace Database\Factories;

use Domain\Pockets\Models\Pocket;
use Illuminate\Database\Eloquent\Factories\Factory;

class DBPocketFactory extends Factory
{
    protected $model = Pocket::class;

    public function definition(): array
    {
        return [
            // TODO: balance should be added by transactions
            'balance' => random_int(0, 100000),
            'currency' => 'USD',
        ];
    }
}
