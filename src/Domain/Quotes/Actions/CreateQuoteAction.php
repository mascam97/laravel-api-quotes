<?php

namespace Domain\Quotes\Actions;

use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Spatie\ModelStates\Exceptions\CouldNotPerformTransition;

class CreateQuoteAction
{
    /**
     * @throws CouldNotPerformTransition
     */
    public function __invoke(QuoteData $data, User $user): Quote
    {
        $quote = new Quote();
        $quote->title = $data->title;
        $quote->content = $data->content;
        $quote->user()->associate($user);
        $quote->save();

        if ($data->published) {
            $quote->state->transitionTo(Published::class);
        }

        return $quote;
    }
}
