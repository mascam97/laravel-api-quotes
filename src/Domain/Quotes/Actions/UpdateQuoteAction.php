<?php

namespace Domain\Quotes\Actions;

use Domain\Quotes\Data\UpdateQuoteData;
use Domain\Quotes\Models\Quote;

class UpdateQuoteAction
{
    public function __invoke(UpdateQuoteData $data, Quote $quote): Quote
    {
        if ($data->title) {
            $quote->title = $data->title;
        }

        if ($data->content) {
            $quote->content = $data->content;
        }

        $quote->update();

        return $quote;
    }
}
