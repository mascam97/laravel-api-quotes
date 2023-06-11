<?php

namespace Domain\Rating\Actions;

use Domain\Quotes\Actions\RefreshQuoteAverageScoreAction;
use Domain\Rating\Data\StoreRatingData;
use Domain\Rating\Models\Rating;
use Illuminate\Database\Eloquent\Model;

class StoreRatingAction
{
    public function __invoke(Model $qualifier, StoreRatingData $data): Rating
    {
        $rateable = $data->getRateable();

        $rating = new Rating();
        $rating->qualifier()->associate($qualifier);
        $rating->rateable()->associate($rateable);
        $rating->score = $data->score;
        $rating->save();

        (new RefreshQuoteAverageScoreAction())->__invoke(quote: $rateable);

        return $rating;
    }
}
