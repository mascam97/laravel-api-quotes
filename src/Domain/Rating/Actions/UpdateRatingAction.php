<?php

namespace Domain\Rating\Actions;

use Domain\Quotes\Actions\RefreshQuoteAverageScoreAction;
use Domain\Rating\Data\UpdateRatingData;
use Domain\Rating\Models\Rating;

class UpdateRatingAction
{
    public function __invoke(Rating $rating, UpdateRatingData $data): Rating
    {
        $rating->score = $data->score;
        $rating->save();

        (new RefreshQuoteAverageScoreAction())->__invoke(quote: $rating->rateable);

        return $rating;
    }
}
