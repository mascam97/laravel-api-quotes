<?php

namespace App\Observers;

use App\Models\Quote;
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

    /**
     * Handle the Quote "restored" event.
     *
     * @param Quote $quote
     * @return void
     */
    public function restored(Quote $quote): void
    {
        // Function for soft delete
    }

    /**
     * Handle the Quote "force deleted" event.
     *
     * @param Quote $quote
     * @return void
     */
    public function forceDeleted(Quote $quote): void
    {
        // Function for soft delete
    }
}
