<?php

namespace Domain\Quotes\DTO;

class QuoteData
{
    public function __construct(
        public ?string $title = null,
        public ?string $content = null,
        public ?int $score = null,
        public ?bool $published = false,
    ) {
    }

    public function quoteIsUnrated(): bool
    {
        return $this->score === 0;
    }
}
