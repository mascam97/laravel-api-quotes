<?php

namespace Domain\Quotes\Actions;

use Domain\Quotes\Models\Quote;

class RefreshQuoteAverageScoreAction
{
    public function __invoke(Quote $quote): bool
    {
        $quote->average_score = $quote->getAverageUserScore();

        return $quote->update();
    }
}
