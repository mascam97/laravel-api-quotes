<?php

namespace Domain\Quotes\Actions;

use Domain\Quotes\Data\StoreQuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Spatie\ModelStates\Exceptions\CouldNotPerformTransition;

class StoreQuoteAction
{
    /**
     * @throws CouldNotPerformTransition
     */
    public function __invoke(StoreQuoteData $data, User $user): Quote
    {
        $quote = new Quote();
        $quote->title = $data->title;
        $quote->content = $data->content;
        $quote->average_score = null;
        $quote->user()->associate($user);
        $quote->save();

        if ($data->published) {
            $quote->state->transitionTo(Published::class);
        }

        return $quote;
    }
}
