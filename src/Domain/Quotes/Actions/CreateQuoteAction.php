<?php

namespace Domain\Quotes\Actions;

use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;

class CreateQuoteAction
{
    public function __invoke(QuoteData $data, User $user): Quote
    {
        $quote = new Quote();
        $quote->title = $data->title;
        $quote->content = $data->content;
        $quote->user()->associate($user);
        $quote->save();

        return $quote;
    }
}
