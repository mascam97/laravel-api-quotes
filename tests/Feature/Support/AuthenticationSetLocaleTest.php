<?php

use Domain\Users\Models\User;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use function PHPUnit\Framework\assertEquals;
use Support\Middleware\SetLocale;

beforeEach(function () {
    Route::get('test-locale-route', function () {
        return 'OK';
    })->middleware(['auth:sanctum', SetLocale::class]);

    $this->user = User::factory()->create(['locale' => 'es_MX']);
    login($this->user);
});

it('use user locale when there is no header', function () {
    get('/test-locale-route')->assertOk();

    assertEquals($this->user->locale, app()->getLocale());
});

it('set user locale when locale from header is invalid', function (?string $headerLocale) {
    $defaultLocale = app()->getLocale();

    get('/test-locale-route', ['Accept-Language' => $headerLocale])->assertOk();

    assertEquals($defaultLocale, app()->getLocale());
    assertEquals($defaultLocale, $this->user->locale);
})->with([
    'de', // unsupported locale
    'invalidLocale', // invalid locale
]);

it('set locale and fallback locale from header and update in user', function (string $locale, string $fallbackLocale) {
    get('/test-locale-route', ['Accept-Language' => $locale])->assertOk();

    assertEquals($locale, app()->getLocale());
    assertEquals($fallbackLocale, app()->getFallbackLocale());
    assertEquals($locale, $this->user->locale);
})->with([
    ['en_US', 'en'],
    ['es', 'es'],
    ['es_MX', 'es'],
]);
