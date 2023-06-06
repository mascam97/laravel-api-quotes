<?php

namespace App\ExternalApi\Quotes\Controllers;

use App\Controller;
use Illuminate\Http\JsonResponse;
use Services\ExternalApi\Exceptions\ExternalApiException;
use Services\ExternalApi\ExternalApiService;

class QuoteController extends Controller
{
    public function __construct(private readonly ExternalApiService $externalApiService)
    {
    }

    /**
     * @throws ExternalApiException
     */
    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->externalApiService->getAllQuotes()]);
    }

    /**
     * @throws ExternalApiException
     */
    public function show(int $quoteId): JsonResponse
    {
        return response()->json(['data' => $this->externalApiService->getQuote($quoteId)]);
    }
}
