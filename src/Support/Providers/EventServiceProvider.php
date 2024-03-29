<?php

namespace Support\Providers;

use Domain\Quotes\Models\Quote;
use Domain\Quotes\Observers\QuoteObserver;
use Domain\Rating\Events\ModelRated;
use Domain\Rating\Listeners\SendEmailModelRatedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ModelRated::class => [
            SendEmailModelRatedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Quote::observe(QuoteObserver::class);
    }
}
