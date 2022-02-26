<?php

namespace App\Actions;

use App\DTO\QuoteData;
use App\Exceptions\InvalidScore;
use App\Models\Quote;
use App\Models\User;

class RateQuoteAction
{
    /**
     * @param QuoteData $data
     * @param Quote $quote
     * @param User $user
     * @return Quote
     * @throws InvalidScore
     */
    public function __invoke(QuoteData $data, Quote $quote, User $user): Quote
    {
        // If the user send 0 in score, the rate is deleted
        if ($data->quoteIsUnrated()) {
            $user->unrate($quote);
        } else {
            if ($user->hasRated($quote)) {
                $user->unrate($quote);
            }
            $user->rate($quote, $data->score);
        }

        return $quote;
    }
}
