<?php

namespace Support\Providers;

use Domain\Users\Models\User;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Services\ExternalApi\ExternalApiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            abstract: ExternalApiService::class,
            concrete: fn () => new ExternalApiService(
                baseUri: (string) config('services.external-api.uri'),
                key: (string) config('services.external-api.key'),
                timeout: (int) config('services.external-api.timeout'),
                retryTimes: (int) config('services.external-api.retry.times'),
                retrySleep: (int) config('services.external-api.retry.sleep')
            )
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(User::class);
        Cashier::calculateTaxes();
    }
}
