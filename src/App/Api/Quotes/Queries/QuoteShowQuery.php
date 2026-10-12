<?php

namespace App\Api\Quotes\Queries;

use Domain\Quotes\Models\Quote;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/** @extends QueryBuilder<Quote> */
class QuoteShowQuery extends QueryBuilder
{
    public function __construct(Request $request)
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
            ]);

        parent::__construct($query, $request);
    }
}
