<?php

namespace App\Api\Quotes\Controllers;

use App\Api\Quotes\Queries\IndexQuoteQuery;
use App\Api\Quotes\Queries\QuoteShowQuery;
use App\Api\Quotes\Resources\QuoteResource;
use App\Controller;
use Domain\Quotes\Actions\StoreQuoteAction;
use Domain\Quotes\Actions\UpdateQuoteAction;
use Domain\Quotes\Data\StoreQuoteData;
use Domain\Quotes\Data\UpdateQuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\ModelStates\Exceptions\CouldNotPerformTransition;
use Support\Metadata\GetQueryMetaDataAction;

/** @authenticated */
class QuoteController extends Controller
{
    /**
     * @bodyParam filter[title] string Filter by title Example: My quote
     * @bodyParam filter[content] string Filter by content Example: My content
     * @bodyParam filter[state] string Filter by state Example: published
     * @bodyParam sort string Sort by fields Example: id,title,created_at
     */
    public function index(IndexQuoteQuery $quoteQuery): AnonymousResourceCollection
    {
        $quotes = $quoteQuery
            ->jsonPaginate()
            ->withQueryString();

        return QuoteResource::collection($quotes)
            ->additional(['meta' => (new GetQueryMetaDataAction())->__invoke($quoteQuery->getQuery())]);
    }

    /**
     * @throws CouldNotPerformTransition
     */
    public function store(StoreQuoteData $data): JsonResponse
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        $quote = (new StoreQuoteAction())->__invoke($data, $authUser);

        return response()->json([
            'message' => trans('message.created', ['attribute' => 'quote']),
            'data' => QuoteResource::make($quote),
        ], 201);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(QuoteShowQuery $quoteQuery, int $quoteId): QuoteResource
    {
        $quote = $quoteQuery->where('id', $quoteId)->firstOrFail();

        $this->authorize('view', $quote);

        return QuoteResource::make($quote);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateQuoteData $data, Quote $quote): JsonResponse
    {
        // user can update a quote if he is the owner
        $this->authorize('update', $quote);

        $quote = (new UpdateQuoteAction())->__invoke($data, $quote);

        return response()->json([
            'data' => QuoteResource::make($quote),
            'message' => trans('message.updated', ['attribute' => 'quote']),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Quote $quote): JsonResponse
    {
        // user can delete a quote if he is the owner
        $this->authorize('delete', $quote);

        $quote->delete();

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'quote']),
        ]);
    }
}
