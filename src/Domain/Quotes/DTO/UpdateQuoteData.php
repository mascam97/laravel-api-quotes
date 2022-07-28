<?php

namespace Domain\Quotes\DTO;

class UpdateQuoteData
{
    public function __construct(
        public ?string $title,
        public ?string $content
    ) {
    }
}
