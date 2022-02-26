<?php

namespace App\Actions;

use App\DTO\QuoteData;
use App\Models\Quote;
use App\Models\User;

class CreateQuoteAction
{
    /**
     * @param QuoteData $data
     * @param User $user
     * @return Quote
     */
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
