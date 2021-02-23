<?php

namespace App\Observers;

use App\Models\Quote;
use Illuminate\Support\Facades\Log;

class QuoteObserver
{
    /**
     * Handle the Quote "created" event.
     *
     * @param  \App\Models\Quote  $quote
     * @return void
     */
    public function created(Quote $quote)
    {
        Log::error("Quote $quote->id was created.");
    }

    /**
     * Handle the Quote "updated" event.
     *
     * @param  \App\Models\Quote  $quote
     * @return void
     */
    public function updated(Quote $quote)
    {
        Log::error("Quote $quote->id was updated.");
    }

    /**
     * Handle the Quote "deleted" event.
     *
     * @param  \App\Models\Quote  $quote
     * @return void
     */
    public function deleted(Quote $quote)
    {
        Log::error("Quote $quote->id was deleted.");
    }

    /**
     * Handle the Quote "restored" event.
     *
     * @param  \App\Models\Quote  $quote
     * @return void
     */
    public function restored(Quote $quote)
    {
        // Function for soft delete
    }

    /**
     * Handle the Quote "force deleted" event.
     *
     * @param  \App\Models\Quote  $quote
     * @return void
     */
    public function forceDeleted(Quote $quote)
    {
        // Function for soft delete
    }
}
