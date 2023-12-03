<?php

namespace App\Web\Quotes\Queries;

use Domain\Quotes\Models\Quote;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class QuoteIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Quote::query()->select([
            'title',
            'content',
            'created_at',
        ]);

        parent::__construct($query, $request);

        $this->allowedFilters(['title', 'content'])
            ->defaultSort('created_at')
            ->allowedSorts('title', 'created_at');
    }
}
