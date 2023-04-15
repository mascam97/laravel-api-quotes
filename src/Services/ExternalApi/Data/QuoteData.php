<?php

namespace Services\ExternalApi\Data;

use Spatie\LaravelData\Data;

class QuoteData extends Data
{
    public function __construct(
        public int $id,
        public string $author,
        public string $content,
    ) {
    }

    public static function fromArray(array $json): self
    {
        return new self(
            id: $json['id'],
            author: $json['author'],
            content: $json['content'],
        );
    }
}
