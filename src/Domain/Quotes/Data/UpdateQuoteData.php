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

    public static function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'min:3', 'max:80', 'unique:quotes,title'],
            'content' => ['nullable', 'string', 'min:3'],
        ];
    }
}
