<?php

use Illuminate\Support\Facades\Http;
use function Pest\Laravel\getJson;
use Services\ExternalApi\Exceptions\ExternalApiException;
use Services\ExternalApi\ExternalApiService;

beforeEach(function () {
    loginExternalApi();

    Http::preventStrayRequests();
});

it('validates id field', function () {
    // $this->expectException(ExternalApiException::class);
    // $this->expectExceptionMessage('External API validation failed');

    $data = jsonFixture('ExternalApi/Quotes');
    $data['data'][0]['id'] = null;

    ExternalApiService::fake(['https://example.com/quotes' => fn () => Http::response($data)]);

    getJson(route('external-api.quotes.index'))->assertServerError();
});
