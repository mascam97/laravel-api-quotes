<?php

namespace Support\Providers;

use Domain\Activities\Policies\ActivityPolicy;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\Policies\QuotePolicy;
use Domain\Rating\Models\Rating;
use Domain\Rating\Policies\RatingPolicy;
use Domain\Users\Models\User;
use Domain\Users\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Activity::class => ActivityPolicy::class,
        Quote::class => QuotePolicy::class,
        Rating::class => RatingPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
    }
}
