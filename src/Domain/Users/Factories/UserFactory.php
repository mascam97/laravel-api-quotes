<?php

namespace Domain\Users\Factories;

use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserFactory
{
    private ?int $amount = null;

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param array<string, mixed> $extra
     * @return User|Collection<int, User>
     */
    public function create(array $extra = []): User|Collection
    {
        if (! $this->amount) {
            /** @var User $user */
            $user = User::factory()->create($extra);
        } else {
            /** @var Collection<int, User> $user */
            $user = User::factory($this->amount)->create($extra);
        }

        return $user;
    }
}
