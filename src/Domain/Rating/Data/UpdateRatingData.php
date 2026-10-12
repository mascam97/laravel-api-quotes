<?php

declare(strict_types=1);

namespace Domain\Rating\Data;

use Spatie\LaravelData\Data;

class UpdateRatingData extends Data
{
    public function __construct(public int $score)
    {
    }
}
