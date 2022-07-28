<?php

namespace App\Api\Quotes\Controllers;

use App\Api\Quotes\Queries\QuoteIndexQuery;
use App\Api\Quotes\Requests\QuoteRequest;
use App\Api\Quotes\Resources\QuoteResource;
use Domain\Quotes\Actions\CreateQuoteAction;
use Domain\Quotes\Actions\RateQuoteAction;
use Domain\Quotes\Actions\UpdateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\DTO\UpdateQuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Exceptions\InvalidScore;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Support\App\Api\Controller;

class QuoteController extends Controller
{
    public function index(QuoteIndexQuery $quoteQuery): AnonymousResourceCollection
    {
        $quotes = $quoteQuery->get();

        return QuoteResource::collection($quotes);
    }

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

    public function show(int $quoteId): QuoteResource
    {
        $query = Quote::query()
            ->whereId($quoteId);

        $quote = QueryBuilder::for($query)
            ->allowedIncludes('user')
            ->firstOrFail();

        return QuoteResource::make($quote);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(QuoteRequest $request, Quote $quote, UpdateQuoteAction $updateQuoteAction): JsonResponse
    {
        // user can update a quote if he is the owner
        $this->authorize('pass', $quote);

        try {
            $quote = $updateQuoteAction->__invoke(new UpdateQuoteData(...$request->validated()), $quote);
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
     * @return JsonResponse
     * @throws InvalidScore
     */
    public function rate(Quote $quote, FormRequest $request, RateQuoteAction $rateQuoteAction)
    {
        // The user can rate from 0 to 5
        // 0 means no rating
        $request->validate([/* @phpstan-ignore-line */
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
