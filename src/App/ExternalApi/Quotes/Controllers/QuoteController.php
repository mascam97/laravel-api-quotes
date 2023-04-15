<?php

namespace App\ExternalApi\Quotes\Controllers;

use App\Controller;
use Illuminate\Http\JsonResponse;
use Services\ExternalApi\Exceptions\ExternalApiException;
use Services\ExternalApi\ExternalApiService;

class QuoteController extends Controller
{
    /**
     * @throws ExternalApiException
     */
    public function index(ExternalApiService $externalApiService): JsonResponse
    {
        return response()->json([
            'data' => $externalApiService->getAllQuotes(),
        ]);
    }

    /**
     * @throws ExternalApiException
     */
    public function show(ExternalApiService $externalApiService, int $quoteId): JsonResponse
    {
        return response()->json([
            'data' => $externalApiService->getQuote($quoteId),
        ]);
    }
}
