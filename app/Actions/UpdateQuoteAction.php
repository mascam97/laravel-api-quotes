<?php

namespace App\Actions;

use App\DTO\QuoteData;
use App\Models\Quote;
use App\Models\User;

class UpdateQuoteAction
{
    /**
     * @param QuoteData $data
     * @param Quote $quote
     * @return Quote
     */
    public function __invoke(QuoteData $data, Quote $quote): Quote
    {
        $quote->title = $data->title;
        $quote->content = $data->content;
        $quote->update();

        return $quote;
    }
}
