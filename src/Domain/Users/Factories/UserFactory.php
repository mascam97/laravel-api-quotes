<?php

namespace Domain\Users\Factories;

use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserFactory
{
    private ?int $amount = null;

    /**
     * @param int $amount
     * @return UserFactory
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function create(array $extra = []): User|Collection
    {
        if (! $this->amount) {
            /** @var User $user */
            $user = User::factory()->create($extra);
        } else {
            /** @var Collection $user */
            $user = User::factory($this->amount)->create($extra);
        }

        return $user;
    }
}
