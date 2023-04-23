<?php

namespace Services\ExternalApi;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Services\Concerns\CanBeFaked;
use Services\ExternalApi\Data\QuoteData;
use Services\ExternalApi\Exceptions\ExternalApiException;

class ExternalApiService
{
    use CanBeFaked;

    public function __construct(
        public readonly string $baseUri,
        public readonly string $key,
        public readonly int $timeout,
        public readonly ?int $retryTimes = null,
        public readonly ?int $retrySleep = null,
    ) {
    }

    public function makeRequest(): PendingRequest
    {
        // TODO: Investigate a way to create logs, activities, reports for the external api
        $request = Http::acceptJson()
            ->withHeaders(['Accept-language' => App::getLocale()])
            ->baseUrl(url: $this->baseUri)
            ->timeout(seconds: $this->timeout);

        if (! is_null($this->retryTimes) && ! is_null($this->retrySleep)) {
            $request->retry(
                times: $this->retryTimes,
                sleepMilliseconds: $this->retrySleep
            );
        }

        return $request;
    }

    /**
     * @throws ExternalApiException
     */
    public function getAllQuotes(): Collection
    {
        $request = $this->makeRequest();

        $response = $request->get('/quotes');

        if ($response->failed()) {
            throw new ExternalApiException();
        }

        return $response->collect('data')
            ->each(fn (array $quote) => QuoteData::fromArray($quote));
    }

    /**
     * @throws ExternalApiException
     */
    public function getQuote(int $id): QuoteData
    {
        $request = $this->makeRequest();

        $response = $request->get("/quotes/{$id}");

        if ($response->failed()) {
            throw new ExternalApiException();
        }

        return QuoteData::fromArray($response->json('data'));
    }
}
