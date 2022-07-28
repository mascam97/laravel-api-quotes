<?php

namespace Domain\Quotes\DTO;

class QuoteData
{
    public function __construct(
        public string $title,
        public string $content,
//        TODO: Create another DTO for only $score and probably a possible comment
        public ?int $score = null,
        public ?bool $published = false,
    ) {
    }

    public function quoteIsUnrated(): bool
    {
        return $this->score === 0;
    }
}
