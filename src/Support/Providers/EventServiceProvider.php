<?php

namespace Support\Providers;

use Domain\Quotes\Models\Quote;
use Domain\Quotes\Observers\QuoteObserver;
use Domain\Rating\Events\ModelRated;
use Domain\Rating\Listeners\SendEmailModelRatedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
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
     *
     * @return void
     */
    public function boot()
    {
        Quote::observe(QuoteObserver::class);
    }
}
