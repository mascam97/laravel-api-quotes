<?php

namespace Domain\Quotes\Actions;

use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;

class UpdateQuoteAction
{
    public function __invoke(QuoteData $data, Quote $quote): Quote
    {
        $quote->title = $data->title;
        $quote->content = $data->content;
        $quote->update();

        return $quote;
    }
}
