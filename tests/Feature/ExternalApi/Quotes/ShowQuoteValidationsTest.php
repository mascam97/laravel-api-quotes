<?php

use Illuminate\Support\Facades\Http;
use function Pest\Laravel\getJson;
use Services\ExternalApi\ExternalApiService;

beforeEach(function () {
    loginExternalApi();

    Http::preventStrayRequests();
});

it('validates id field', function () {
    // $this->expectException(ExternalApiException::class);
    // $this->expectExceptionMessage('External API validation failed');

    $data = fixture('ExternalApi/Quote');
    $data['data']['id'] = null;

    ExternalApiService::fake(['https://example.com/quotes/10' => Http::response($data)]);

    getJson(route('external-api.quotes.show', ['quote' => 10]))->assertServerError();
});
