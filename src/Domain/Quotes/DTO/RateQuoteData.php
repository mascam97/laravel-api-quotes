<?php

namespace Domain\Quotes\DTO;

class RateQuoteData
{
    public function __construct(
//        TODO: Add a possible comment
        public int $score,
    ) {
    }

    public function quoteIsUnrated(): bool
    {
        return $this->score === 0;
    }
}
