<?php

namespace Support\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
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
        $appNotInProduction = ! app()->isProduction();

        // Ensure an exception is thrown when lazy loading is attempted (n+1 problem)
        Model::preventLazyLoading($appNotInProduction);
        // Ensure an exception is thrown when a non-existing attribute is accessed
        Model::shouldBeStrict($appNotInProduction);
    }
}
