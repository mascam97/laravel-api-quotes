<?php

namespace Domain\Gifts\Data;

use Spatie\LaravelData\Data;

class StoreGiftData extends Data
{
    public function __construct(
        public ?string $note,
        public int $amount,
        public string $currency,
    ) {
    }

    /**
     * @return array<string, string[]>
     */
    public static function rules(): array
    {
        return [
            'note' => ['required', 'string', 'min:3', 'max:255'],
            // TODO: Add a logic about payment method, where the money is supposed to come from
            'amount' => ['required', 'integer', 'min:100'],
            // TODO: Add validated currency rule
            'currency' => ['required', 'string'],
        ];
    }
}
