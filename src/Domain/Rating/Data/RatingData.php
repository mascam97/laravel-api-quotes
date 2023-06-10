<?php

namespace Domain\Rating\Data;

use Domain\Quotes\Models\Quote;
use Spatie\LaravelData\Data;

class RatingData extends Data
{
    public function __construct(
        public int $score,
        public int $rateableId,
        public string $rateableType,
    ) {
    }

    public function getRateable(): Quote
    {
        return Quote::query()
            ->whereId($this->rateableId)
            ->firstOrFail();
    }
}
