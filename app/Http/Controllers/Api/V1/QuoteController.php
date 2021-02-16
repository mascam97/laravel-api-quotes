<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Quote;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\QuoteRequest;

class QuoteController extends Controller
{
    protected $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function index()
    {
        return response()->json($this->quote->paginate());
    }

    public function store(QuoteRequest $request)
    {
        $quote = $this->quote->create($request->all());
        return response()->json($quote, 201);
    }

    public function show(Quote $quote)
    {
        return response()->json($quote);
    }

    public function update(QuoteRequest $request, Quote $quote)
    {
        $quote->update($request->all());

        return response()->json($quote);
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return response()->json(null, 204);
    }
}
