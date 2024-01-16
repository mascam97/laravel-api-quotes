<?php

namespace Domain\Pockets;

use Domain\Gifts\Models\Gift;
use Domain\Pockets\Events\MoneyAdded;
use Domain\Pockets\Events\PocketCreated;
use Domain\Pockets\Models\Pocket;
use Domain\Users\Models\User;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class PocketAggregateRoot extends AggregateRoot
{
    protected int $balance = 0;

    public function createPocket(User $user, string $currency): static
    {
        $this->recordThat(new PocketCreated($user->getKey(), $currency));

        return $this;
    }

    public function addMoney(Pocket $pocket, Gift $gift): static
    {
        $this->recordThat(new MoneyAdded($pocket->getKey(), $gift->getKey(), $gift->amount, $gift->currency));

        return $this;
    }

    public function applyMoneyAdded(MoneyAdded $event): void
    {
        $this->balance += $event->amount;
    }
}
