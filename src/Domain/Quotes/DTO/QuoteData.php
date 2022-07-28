<?php

namespace Domain\Quotes\DTO;

class QuoteData
{
    public function __construct(
        public string $title,
        public string $content,
        public ?bool $published = false,
    ) {
    }
}
