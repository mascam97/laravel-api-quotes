<?php

namespace Domain\Quotes\DTO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\DataTransferObject;

class QuoteData extends DataTransferObject
{
    public ?string $title;

    public ?string $content;

    public ?int $score;

    public static function fromRequest(FormRequest|Request $request): self
    {
        return new static([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'score' => $request->input('score'),
        ]);
    }

    public function quoteIsUnrated(): bool
    {
        return $this->score === 0;
    }
}
