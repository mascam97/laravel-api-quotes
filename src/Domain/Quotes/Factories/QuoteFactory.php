<?php

namespace Domain\Quotes\Factories;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

class QuoteFactory
{
    /** @var array<string, mixed> */
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

    /**
     * @param array<string, mixed> $extra
     * @return Quote|Collection<int, Quote>
     */
    public function create(array $extra = []): Quote|Collection
    {
        if (! $this->amount) {
            /** @var Quote $quote */
            $quote = Quote::factory()->create(array_merge($this->properties, $extra));
        } else {
            /** @var Collection<int, Quote> $quote */
            $quote = Quote::factory($this->amount)->create(array_merge($this->properties, $extra));
        }

        return $quote;
    }
}
