<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateQuoteAction;
use App\Actions\RateQuoteAction;
use App\Actions\UpdateQuoteAction;
use App\DTO\QuoteData;
use App\Exceptions\InvalidScore;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\QuoteRequest;
use App\Http\Resources\V1\QuoteCollection;
use App\Http\Resources\V1\QuoteResource;
use App\Models\Quote;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

// TODO: Delete versioning and some duplicated routes, and use the QueryBuilder instead
class QuoteController extends Controller
{
    protected Quote $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return QuoteCollection
     */
    public function index(): QuoteCollection
    {
        $quotes = QueryBuilder::for(Quote::class)
            ->allowedFilters(['title', 'content'])
            ->allowedIncludes('user')
            ->allowedSorts('id', 'title')
            ->get();

        return new QuoteCollection($quotes);
    }

    /**
     * @param QuoteRequest $request
     * @param CreateQuoteAction $createQuoteAction
     * @return JsonResponse
     */
    public function store(QuoteRequest $request, CreateQuoteAction $createQuoteAction): JsonResponse
    {
        try {
            $quote = $createQuoteAction->__invoke(QuoteData::fromRequest($request), $request->user());
        } catch (\Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Server error',
            ], 422);
        }

        return response()->json([
            'data' => new QuoteResource($quote),
            'message' => trans('message.created', ['attribute' => 'quote']),
        ], 201);
    }

    /**
     * @param Quote $quote
     * @return JsonResponse
     */
    public function show(Quote $quote): JsonResponse
    {
        return response()->json(new QuoteResource($quote));
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
            $quote = $updateQuoteAction->__invoke(QuoteData::fromRequest($request), $quote);
        } catch (\Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Server error',
            ], 422);
        }

        return response()->json([
            'data' => new QuoteResource($quote),
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

        $data = QuoteData::fromRequest($request);
        $rateQuoteAction->__invoke($data, $quote, $request->user());

        if ($data->quoteIsUnrated()) {
            return response()->json([
                'data' => new QuoteResource($quote),
                'message' => trans('message.rating.unrated', [
                    'attribute' => 'quote',
                    'id' => $quote->id,
                ]),
            ]);
        }

        return response()->json([
            'data' => new QuoteResource($quote),
            'message' => trans('message.rating.rated', [
                'attribute' => 'quote',
                'id' => $quote->id,
                'score' => $data->score,
            ]),
        ]);
    }
}
