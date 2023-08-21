<?php

namespace Services\ExternalApi\Data;

use Spatie\LaravelData\Data;

class QuoteData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
        public string $content,
        public string $image_url,
        public int $year,
        public string $info_url
    ) {
    }

    public static function fromArray(array $json): self
    {
        return new self(
            id: $json['id'],
            title: $json['title'],
            author: $json['author'],
            content: $json['content'],
            image_url: $json['image_url'],
            year: $json['year'],
            info_url: $json['info_url'],
        );
    }
}
