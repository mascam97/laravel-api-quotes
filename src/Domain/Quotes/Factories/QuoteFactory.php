<?php

namespace Domain\Quotes\Factories;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

class QuoteFactory
{
    private array $properties = [];

    private ?int $amount = null;

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function withUser(User $user): self
    {
        $this->properties += [
            'user_id' => $user->getKey(),
        ];

        return $this;
    }

    public function withState(string $state): self
    {
        $this->properties += [
            'state' => $state,
        ];

        return $this;
    }

    public function create(array $extra = []): Quote|Collection
    {
        if (! $this->amount) {
            /** @var Quote $quote */
            $quote = Quote::factory()->create(array_merge($this->properties, $extra));
        } else {
            /** @var Collection $quote */
            $quote = Quote::factory($this->amount)->create(array_merge($this->properties, $extra));
        }

        return $quote;
    }
}
