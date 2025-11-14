<?php

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Services\ExternalApi\Data\QuoteData;
use Services\ExternalApi\Exceptions\ExternalApiException;
use Services\ExternalApi\ExternalApiService;

it('can build a new External Api Service', function (string $string) {
    expect(
        new ExternalApiService(
            baseUri: $string,
            key:     $string,
            timeout: 10,
        )
    )->toBeInstanceOf(ExternalApiService::class);
})->with('strings');

it('can create a Pending Request', function (string $string) {
    $service = new ExternalApiService(
        baseUri: $string,
        key:     $string,
        timeout: 10,
    );

    expect(
        $service->makeRequest(),
    )->toBeInstanceOf(PendingRequest::class);
})->with('strings');

it('can resolve a External Api Service from the container', function () {
    expect(
        resolve(ExternalApiService::class)
    )->toBeInstanceOf(ExternalApiService::class);
});

it('can create a Pending Request from the Resolved Service', function () {
    expect(
        resolve(ExternalApiService::class)->makeRequest(),
    )->toBeInstanceOf(PendingRequest::class);
});

it('resolves as a singleton only', function () {
    $service = resolve(ExternalApiService::class);

    expect(
        resolve(ExternalApiService::class)
    )->toEqual($service);
});

it('cannot get quotes without the service up', function () {
    /** @var ExternalApiService $service */
    $service = resolve(ExternalApiService::class);

    $service->getAllQuotes();
})->throws(ExternalApiException::class);

it('cannot get quote without the service up', function () {
    /** @var ExternalApiService $service */
    $service = resolve(ExternalApiService::class);

    $service->getQuote(10);
})->throws(ExternalApiException::class);

it('can get a quote', function () {
    ExternalApiService::fake([
        'https://example.com/quotes/1' => fn () => Http::response(
            body:   fixture('ExternalApi/Quote'),
        ),
    ]);

    /** @var ExternalApiService $service */
    $service = resolve(ExternalApiService::class);

    expect(
        $service->getQuote(1),
    )->toBeInstanceOf(QuoteData::class);
});

it('can get quotes', function () {
    ExternalApiService::fake([
        'https://example.com/quotes' => fn () => Http::response(
            body:   fixture('ExternalApi/Quotes'),
        ),
    ]);

    /** @var ExternalApiService $service */
    $service = resolve(ExternalApiService::class);

    expect(
        $service->getAllQuotes(),
    )->toBeInstanceOf(Collection::class);
});
