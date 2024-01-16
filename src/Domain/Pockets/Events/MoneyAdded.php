<?php

namespace Domain\Pockets\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MoneyAdded extends ShouldBeStored
{
    public function __construct(
        public int $pocketId,
        public int $giftId,
        public int $amount,
        public string $currency,
    ) {
    }
}
