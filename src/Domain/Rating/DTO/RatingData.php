<?php

namespace Domain\Rating\DTO;

class RatingData
{
    public function __construct(
        public int $score
    ) {
    }
}
