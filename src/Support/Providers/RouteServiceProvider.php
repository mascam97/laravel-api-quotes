<?php

namespace Support\Providers;

use Domain\Users\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     */
    final public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::prefix('api-admin')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api-admin.php'));

            Route::prefix('external-api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/external-api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            /** @var ?User $authUser */
            $authUser = $request->user();

            return Limit::perMinute(60)->by(optional($authUser)->getKey() ?: $request->ip());
        });

        RateLimiter::for('api-admin', function (Request $request) {
            /** @var ?User $authUser */
            $authUser = $request->user();

            return Limit::perMinute(30)->by(optional($authUser)->getKey() ?: $request->ip());
        });

        RateLimiter::for('api-analytics', function (Request $request) {
            /** @var ?User $authUser */
            $authUser = $request->user();

            return Limit::perMinute(30)->by(optional($authUser)->getKey() ?: $request->ip());
        });

        RateLimiter::for('external-api', function (Request $request) {
            /** @var ?User $authUser */
            $authUser = $request->user();

            return Limit::perMinute(15)->by(optional($authUser)->getKey() ?: $request->ip());
        });

        RateLimiter::for('passport.token', fn (Request $request) => [
            Limit::perMinute(500),
            Limit::perMinute(3)->by($request->input('username')),
        ]);

        RateLimiter::for('downloads', fn (Request $request) => Limit::perMinute(10));
    }
}
