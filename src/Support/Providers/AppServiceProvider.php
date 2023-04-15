<?php

namespace Support\Providers;

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
                baseUri: (string) config('services.external-api.uri'), /* @phpstan-ignore-line */
                key: (string) config('services.external-api.key'), /* @phpstan-ignore-line */
                timeout: (int) config('services.external-api.timeout'), /* @phpstan-ignore-line */
                retryTimes: (int) config('services.external-api.retry.times'), /* @phpstan-ignore-line */
                retrySleep: (int) config('services.external-api.retry.sleep') /* @phpstan-ignore-line */
            )
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
