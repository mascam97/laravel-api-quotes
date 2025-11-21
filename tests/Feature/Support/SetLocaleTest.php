<?php

use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use function PHPUnit\Framework\assertEquals;
use Support\Middleware\SetLocale;

beforeEach(function () {
    Route::get('test-locale-route', function () {
        return 'OK';
    })->middleware(SetLocale::class);
});

it('set default locale when locale from header is invalid', function (?string $headerLocale) {
    $defaultLocale = app()->getLocale();

    get('/test-locale-route', ['Accept-Language' => $headerLocale])->assertOk();

    assertEquals($defaultLocale, app()->getLocale());
})->with([
    null, // undefined locale
    'de', // unsupported locale
    'invalidLocale', // invalid locale
]);

it('set locale and fallback locale from header', function (string $locale, string $fallbackLocale) {
    get('/test-locale-route', ['Accept-Language' => $locale])->assertOk();

    assertEquals($locale, app()->getLocale());
    assertEquals($fallbackLocale, app()->getFallbackLocale());
})->with([
    ['en_US', 'en'],
    ['es', 'es'],
    ['es_MX', 'es'],
]);

// TODO: Improve test with an authenticated user
