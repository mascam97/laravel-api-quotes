<?php

namespace Domain\Quotes\Observers;

use Domain\Quotes\Models\Quote;
use Illuminate\Support\Facades\Log;

class QuoteObserver
{
    /**
     * Handle the Quote "created" event.
     *
     * @param Quote $quote
     * @return void
     */
    public function created(Quote $quote): void
    {
        Log::error("Quote $quote->id was created.");
    }

    /**
     * Handle the Quote "updated" event.
     *
     * @param Quote $quote
     * @return void
     */
    public function updated(Quote $quote): void
    {
        Log::error("Quote $quote->id was updated.");
    }

    /**
     * Handle the Quote "deleted" event.
     *
     * @param Quote $quote
     * @return void
     */
    public function deleted(Quote $quote): void
    {
        Log::error("Quote $quote->id was deleted.");
    }
}
