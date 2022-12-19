<?php

namespace Domain\Rating\Data;

use Spatie\LaravelData\Data;

class RatingData extends Data
{
    public function __construct(
        public int $score
    ) {
    }
}
