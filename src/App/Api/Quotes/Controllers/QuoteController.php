<?php

namespace App\Api\Quotes\Controllers;

use App\Api\Quotes\Queries\QuoteIndexQuery;
use App\Api\Quotes\Resources\QuoteResource;
use App\Controller;
use Domain\Quotes\Actions\StoreQuoteAction;
use Domain\Quotes\Actions\UpdateQuoteAction;
use Domain\Quotes\Data\StoreQuoteData;
use Domain\Quotes\Data\UpdateQuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\ModelStates\Exceptions\CouldNotPerformTransition;
use Spatie\QueryBuilder\QueryBuilder;

class QuoteController extends Controller
{
    public function me(Request $request): AnonymousResourceCollection
    {
        /** @var User $authUser */
        $authUser = $request->user();

        $query = Quote::query()
            ->select([
                'id',
                'title',
                'content',
                'state',
                'average_score',
                'user_id',
                'created_at',
                'updated_at',
            ])
            ->whereUser($authUser);

        // TODO: Search a way to use QuoteIndexQuery
        $quotes = QueryBuilder::for($query)
            ->allowedFilters(['title', 'content', 'state'])
            ->allowedSorts('id', 'title', 'created_at')
            ->paginate();

        return QuoteResource::collection($quotes);
    }

    public function index(QuoteIndexQuery $quoteQuery): AnonymousResourceCollection
    {
        $quotes = $quoteQuery->paginate();

        return QuoteResource::collection($quotes);
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

    public function show(int $quoteId): QuoteResource
    {
        $query = Quote::query()
            ->select([
                'id',
                'title',
                'content',
                'state',
                'average_score',
                'user_id',
                'created_at',
                'updated_at',
            ])
            ->whereState(Published::$name)
            ->whereId($quoteId);

        $quote = QueryBuilder::for($query)
            ->allowedIncludes('user')
            ->firstOrFail();

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
