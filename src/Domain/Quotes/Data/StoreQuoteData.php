<?php

namespace Domain\Quotes\Data;

use Spatie\LaravelData\Data;

class StoreQuoteData extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public ?bool $published = false,
    ) {
    }
}
