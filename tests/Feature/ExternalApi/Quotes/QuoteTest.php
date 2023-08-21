<?php

use Illuminate\Support\Facades\Http;
use function Pest\Laravel\getJson;
use Services\ExternalApi\ExternalApiService;

it('cannot authorize guest', function () {
    getJson(route('external-api.quotes.index'))
        ->assertUnauthorized();

    getJson(route('external-api.quotes.show', ['quote' => 10]))
        ->assertUnauthorized();
});

// TODO: Support more errors from external api

//it('cannot show undefined data', function () {
//    login();
//    ExternalApiService::fake(['https://example.com/quotes/10' => Http::response(status: 404)]);
//
//    getJson(route('external-api.quotes.show', ['quote' => 10]))
//        ->assertNotFound();
//});
