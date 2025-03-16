<?php

namespace Services\ExternalApi;

use Domain\Quotes\Models\Quote;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Services\Concerns\CanBeFaked;
use Services\ExternalApi\Data\QuoteData;
use Services\ExternalApi\Exceptions\ExternalApiException;

readonly class ExternalApiService
{
    use CanBeFaked;

    public function __construct(
        public string $baseUri,
        public string $key,
        public int $timeout,
        public ?int $retryTimes = null,
        public ?int $retrySleep = null,
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
     * @return Collection<int, Quote>
     */
    public function getAllQuotes(): Collection
    {
        $request = $this->makeRequest();

        $response = $request->get('/quotes');

        if ($response->failed()) {
            throw ExternalApiException::responseFailed();
        }

        try {
            return $response->collect('data')
                ->each(fn (array $quote) => QuoteData::fromArray($quote));
        } catch (ValidationException $e) {
            report($e);

            throw ExternalApiException::validationFailed();
        }
    }

    /**
     * @throws ExternalApiException
     */
    public function getQuote(int $id): QuoteData
    {
        $request = $this->makeRequest();

        $response = $request->get("/quotes/{$id}");

        if ($response->failed()) {
            throw ExternalApiException::responseFailed();
        }

        try {
            return QuoteData::fromArray($response->json('data'));
        } catch (ValidationException $e) {
            report($e);

            throw ExternalApiException::validationFailed();
        }
    }
}
