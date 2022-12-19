<?php

namespace Domain\Quotes\Data;

use Spatie\LaravelData\Data;

class UpdateQuoteData extends Data
{
    public function __construct(
        public ?string $title,
        public ?string $content,
    ) {
    }
}
