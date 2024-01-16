<?php

namespace Domain\Pockets\Projectors;

use Domain\Pockets\Events\MoneyAdded;
use Domain\Pockets\Events\PocketCreated;
use Domain\Pockets\Models\Pocket;
use Domain\Users\Models\User;
use Money\Currency;
use Money\Money;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PocketProjector extends Projector
{
    public function onPocketCreated(PocketCreated $event): void
    {
        $pocket = new Pocket();
        $pocket->balance = 0;
        $pocket->currency = $event->currency;

        $pocket->save();

        $user = User::query()->findOrFail($event->userId);

        $user->pocket()->associate($pocket);
        $user->save();
    }

    public function onMoneyAdded(MoneyAdded $event): void
    {
        /** @var Pocket $pocket */
        $pocket = Pocket::query()->findOrFail($event->pocketId);

        // TODO: Add event and exception when currency is different

        $currentBalance = new Money($pocket->balance, new Currency($pocket->currency)); /* @phpstan-ignore-line */
        $moneyToAdd = new Money($event->amount, new Currency($event->currency)); /* @phpstan-ignore-line */

        $pocket->balance = $currentBalance->add($moneyToAdd)->getAmount(); /* @phpstan-ignore-line */

        $pocket->save();
    }
}
