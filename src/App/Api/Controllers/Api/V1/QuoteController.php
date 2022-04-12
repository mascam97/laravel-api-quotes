<?php

namespace App\Api\Controllers\Api\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\V1\QuoteRequest;
use App\Api\Resources\V1\QuoteResource;
use Domain\Quotes\Actions\CreateQuoteAction;
use Domain\Quotes\Actions\RateQuoteAction;
use Domain\Quotes\Actions\UpdateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\Models\Quote;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Support\Exceptions\InvalidScore;

class QuoteController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $quotes = QueryBuilder::for(Quote::class)
            ->allowedFilters(['title', 'content', 'user_id'])
            ->allowedIncludes('user')
            ->allowedSorts('id', 'title')
            ->get();

        return QuoteResource::collection($quotes);
    }

    /**
     * @param QuoteRequest $request
     * @param CreateQuoteAction $createQuoteAction
     * @return JsonResponse
     */
    public function store(QuoteRequest $request, CreateQuoteAction $createQuoteAction): JsonResponse
    {
        try {
            $quote = $createQuoteAction->__invoke(new QuoteData(...$request->validated()), $request->user());
        } catch (\Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Server error',
            ], 422);
        }

        return response()->json([
            'data' => QuoteResource::make($quote),
            'message' => trans('message.created', ['attribute' => 'quote']),
        ], 201);
    }

    /**
     * @param int $quote_id
     * @return QuoteResource
     */
    public function show(int $quote_id): QuoteResource
    {
        $quote = QueryBuilder::for(Quote::query()->where('id', $quote_id))
            ->allowedIncludes('user')
            ->firstOrFail();

        return QuoteResource::make($quote);
    }

    /**
     * @param QuoteRequest $request
     * @param Quote $quote
     * @param UpdateQuoteAction $updateQuoteAction
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(QuoteRequest $request, Quote $quote, UpdateQuoteAction $updateQuoteAction): JsonResponse
    {
        // user can update a quote if he is the owner
        $this->authorize('pass', $quote);

        try {
            $quote = $updateQuoteAction->__invoke(new QuoteData(...$request->validated()), $quote);
        } catch (\Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Server error',
            ], 422);
        }

        return response()->json([
            'data' => QuoteResource::make($quote),
            'message' => trans('message.updated', ['attribute' => 'quote']),
        ]);
    }

    /**
     * @param Quote $quote
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Quote $quote): JsonResponse
    {
        // user can delete a quote if he is the owner
        $this->authorize('pass', $quote);

        $quote->delete();

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'quote']),
        ]);
    }

    /**
     * @param Quote $quote
     * @param Request $request
     * @param RateQuoteAction $rateQuoteAction
     * @return JsonResponse
     * @throws InvalidScore
     */
    public function rate(Quote $quote, Request $request, RateQuoteAction $rateQuoteAction)
    {
        // The user can rate from 0 to 5
        // 0 means no rating
        $request->validate([
            'score' => 'required|integer',
        ]);

        $data = new QuoteData(...$request->validated());
        $rateQuoteAction->__invoke($data, $quote, $request->user());

        if ($data->quoteIsUnrated()) {
            return response()->json([
                'data' => QuoteResource::make($quote),
                'message' => trans('message.rating.unrated', [
                    'attribute' => 'quote',
                    'id' => $quote->id,
                ]),
            ]);
        }

        return response()->json([
            'data' => QuoteResource::make($quote),
            'message' => trans('message.rating.rated', [
                'attribute' => 'quote',
                'id' => $quote->id,
                'score' => $data->score,
            ]),
        ]);
    }
}
