<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\QuoteRequest;
use App\Http\Resources\V2\QuoteCollection;
use App\Http\Resources\V2\QuoteResource;
use App\Models\Quote;
use Illuminate\Http\Request;

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

    public function rate(Quote $quote, Request $request)
    {
        // The user can rate from 0 to 5
        // 0 means no rating
        $validated = $request->validate([
            'score' => 'required|integer',
        ]);

        // If the user send 0 in score, the rate is deleted
        if ($request->score == 0) {
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
