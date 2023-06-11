<?php

namespace App\Api\PublicQuotes\Controllers;

use App\Api\PublicQuotes\Queries\PublicQuoteIndexQuery;
use App\Api\PublicQuotes\Queries\PublicQuoteShowQuery;
use App\Api\PublicQuotes\Resources\PublicQuoteResource;
use App\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicQuotesController extends Controller
{
    public function index(PublicQuoteIndexQuery $quoteQuery): AnonymousResourceCollection
    {
        $quotes = $quoteQuery->paginate();

        return PublicQuoteResource::collection($quotes);
    }

    public function show(PublicQuoteShowQuery $quoteQuery, int $quoteId): PublicQuoteResource
    {
        $quote = $quoteQuery->where('id', $quoteId)->firstOrFail();

        return PublicQuoteResource::make($quote);
    }
}
