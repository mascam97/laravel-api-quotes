<?php

namespace Domain\Pockets\Factories;

use Domain\Pockets\Models\Pocket;
use Illuminate\Database\Eloquent\Collection;

class PocketFactory
{
    private array $properties = [];

    private ?int $balance = null;

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function create(array $extra = []): Pocket|Collection
    {
        if (! $this->balance) {
            /** @var Pocket $pocket */
            $pocket = Pocket::factory()->create(array_merge($this->properties, $extra));
        } else {
            /** @var Collection $pocket */
            $pocket = Pocket::factory($this->balance)->create(array_merge($this->properties, $extra));
        }

        return $pocket;
    }
}
