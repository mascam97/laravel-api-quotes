<?php

namespace Domain\Rating\Actions;

use Domain\Quotes\Actions\RefreshQuoteAverageScoreAction;
use Domain\Rating\Data\RatingData;
use Domain\Rating\Models\Rating;
use Illuminate\Database\Eloquent\Model;

class UpdateOrCreateRatingAction
{
    public function __invoke(Model $qualifier, RatingData $data): Rating
    {
        $rateable = $data->getRateable();

        $storedRating = Rating::query()->whereQualifier($qualifier)->whereRateable($rateable)->first();

        if ($storedRating instanceof Rating) {
            $storedRating->score = $data->score;

            $storedRating->save();

            return $storedRating;
        }

        $rating = new Rating();

        $rating->qualifier()->associate($qualifier);
        $rating->rateable()->associate($rateable);
        $rating->score = $data->score;
        $rating->save();

        (new RefreshQuoteAverageScoreAction())->__invoke(quote: $rateable);

        return $rating;
    }
}
