<?php

namespace Domain\Quotes\Actions;

use Domain\Quotes\DTO\RateQuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Exceptions\InvalidScore;
use Domain\Users\Models\User;

class RateQuoteAction
{
    /**
     * @throws InvalidScore
     */
    public function __invoke(RateQuoteData $data, Quote $quote, User $user): Quote
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
