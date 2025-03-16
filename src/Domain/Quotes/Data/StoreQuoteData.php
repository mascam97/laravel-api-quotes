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

    /**
     * @return array<string, string[]>
     */
    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:80', 'unique:quotes,title'],
            'content' => ['required', 'string', 'min:3'],
        ];
    }
}
