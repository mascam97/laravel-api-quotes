<?php

namespace Support\Providers;

use Domain\Quotes\Models\Quote;
use Domain\Quotes\Policies\QuotePolicy;
use Domain\Rating\Models\Rating;
use Domain\Rating\Policies\RatingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Quote::class => QuotePolicy::class,
        Rating::class => RatingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
    }
}
