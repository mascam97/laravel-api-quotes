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
        /** @var ?User $authUser */
        $authUser = $request->user();

        if ($request->hasHeader('Accept-Language')) {
            $locale = $request->getPreferredLanguage(config('app.available_locales')); /* @phpstan-ignore-line */

            if ($locale && $authUser) {
                $authUser->locale = $locale;
                $authUser->update();
            }
        } else {
            $locale = $authUser?->locale;
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
