<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\QuoteRequest;
use App\Http\Resources\V1\QuoteCollection;
use App\Http\Resources\V1\QuoteResource;
use App\Models\Quote;

class QuoteController extends Controller
{
    protected $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function index()
    {
        $data = new QuoteCollection($this->quote::paginate(10));

        return response()->json($data);
    }

    public function store(QuoteRequest $request)
    {
        $quote = $request->user()->quotes()->create($request->all());

        return response()->json([
            'data' => new QuoteResource($quote),
            'message' => trans('message.created', ['attribute' => 'quote']),
        ], 201);
    }

    public function show(Quote $quote)
    {
        return response()->json(new QuoteResource($quote));
    }

    public function update(QuoteRequest $request, Quote $quote)
    {
        // user can update a quote if he is the owner
        $this->authorize('pass', $quote);

        $quote->update($request->all());

        return response()->json([
            'data' => new QuoteResource($quote),
            'message' => trans('message.updated', ['attribute' => 'quote']),
        ]);
    }

    public function destroy(Quote $quote)
    {
        // user can delete a quote if he is the owner
        $this->authorize('pass', $quote);

        $quote->delete();

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'quote']),
        ]);
    }
}
