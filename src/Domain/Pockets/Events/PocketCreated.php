<?php

namespace Domain\Pockets\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PocketCreated extends ShouldBeStored
{
    public function __construct(
        public int $userId,
        public string $currency
    ) {
    }
}
