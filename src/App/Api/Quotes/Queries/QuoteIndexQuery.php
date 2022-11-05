<?php

namespace App\Api\Quotes\Queries;

use Domain\Quotes\Models\Quote;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class QuoteIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Quote::query();

        parent::__construct($query, $request);

        $this->allowedFilters(['title', 'content', 'user_id'])
            ->allowedIncludes('user')
            ->allowedSorts('id', 'title', 'created_at');
    }
}
