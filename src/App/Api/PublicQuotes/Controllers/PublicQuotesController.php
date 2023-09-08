<?php

namespace App\Api\PublicQuotes\Controllers;

use App\Api\PublicQuotes\Queries\PublicQuoteIndexQuery;
use App\Api\PublicQuotes\Queries\PublicQuoteShowQuery;
use App\Api\PublicQuotes\Resources\PublicQuoteResource;
use App\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** @authenticated */
class PublicQuotesController extends Controller
{
    /**
     * @bodyParam filter[title] string Filter by title Example: Quote
     * @bodyParam filter[content] string Filter by content Example: Content
     * @bodyParam filter[user_id] int Filter by user_id Example: 1
     * @bodyParam sort string Sort by fields Example: id,title and created at
     */
    public function index(PublicQuoteIndexQuery $quoteQuery): AnonymousResourceCollection
    {
        $quotes = $quoteQuery->paginate();

        return PublicQuoteResource::collection($quotes);
    }

    /**
     * @bodyParam include string Include relationships Example: user
     */
    public function show(PublicQuoteShowQuery $quoteQuery, int $quoteId): PublicQuoteResource
    {
        $quote = $quoteQuery->where('id', $quoteId)->firstOrFail();

        return PublicQuoteResource::make($quote);
    }
}
