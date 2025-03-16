<?php

namespace Services\ExternalApi\Data;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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

    /**
     * @throws ValidationException
     * @param array<string, mixed> $json
     */
    public static function fromArray(array $json): self
    {
        Validator::make($json, [
            'id' => 'required|integer',
            'title' => 'required|string',
            'author' => 'required|string',
            'content' => 'required|string',
            // TODO: Validate image_url should return a image
            'image_url' => 'required|string|url|active_url',
            'year' => 'required|integer|between:1000,2021',
            // TODO: Validate info_url should return a safe url
            'info_url' => 'required|string|url|active_url',
        ])->validate();

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
