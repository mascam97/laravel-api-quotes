<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\QuoteRequest;
use App\Http\Resources\V1\QuoteCollection;
use App\Http\Resources\V1\QuoteResource;
use App\Models\Quote;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

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
}
