<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\QuoteRequest;
use App\Http\Resources\V2\QuoteCollection;
use App\Http\Resources\V2\QuoteResource;
use App\Models\Quote;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    protected Quote $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = new QuoteCollection($this->quote::paginate(10));

        return response()->json($data);
    }

    /**
     * @param QuoteRequest $request
     * @return JsonResponse
     */
    public function store(QuoteRequest $request): JsonResponse
    {
        $quote = $request->user()->quotes()->create($request->all());

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
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(QuoteRequest $request, Quote $quote): JsonResponse
    {
        // user can update a quote if he is the owner
        $this->authorize('pass', $quote);

        $quote->update($request->all());

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
     * @return JsonResponse|void
     */
    public function rate(Quote $quote, Request $request)
    {
        // The user can rate from 0 to 5
        // 0 means no rating
        $validated = $request->validate([
            'score' => 'required|integer',
        ]);

        // If the user send 0 in score, the rate is deleted
        if ($request->score === 0) {
            $request->user()->unrate($quote);

            return response()->json([
                'data' => new QuoteResource($quote),
                'message' => trans('message.rating.unrated', [
                    'attribute' => 'quote',
                    'id' => $quote->id,
                ]),
            ]);
        }

        if ($request->score !== 0) {
            if ($request->user()->hasRated($quote)) {
                $request->user()->unrate($quote);
            }

            $request->user()->rate($quote, $request->score);

            return response()->json([
                'data' => new QuoteResource($quote),
                'message' => trans('message.rating.rated', [
                    'attribute' => 'quote',
                    'id' => $quote->id,
                    'score' => $request->score,
                ]),
            ]);
        }
    }
}
