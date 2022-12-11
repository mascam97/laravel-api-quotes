<?php

namespace Support\Middleware;

use Closure;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->getPreferredLanguage(config('app.available_locales')); /* @phpstan-ignore-line */

        /** @var ?User $authUser */
        $authUser = $request->user();

        if ($locale && $authUser) {
            $authUser->locale = $locale;
            $authUser->update();
        }

        if (! $locale && $authUser) {
            $locale = $authUser->locale;
        }

        if ($locale) {
            self::setApplicationLocale($locale);
        }

        return $next($request);
    }

    public static function setApplicationLocale(string $locale): void
    {
        app()->setLocale($locale);
        app()->setFallbackLocale(Str::substr($locale, 0, 2));
    }
}
