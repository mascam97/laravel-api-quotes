<?php

namespace Domain\Rating\Data;

use Spatie\LaravelData\Data;

class UpdateRatingData extends Data
{
    public function __construct(public int $score)
    {
    }
}
