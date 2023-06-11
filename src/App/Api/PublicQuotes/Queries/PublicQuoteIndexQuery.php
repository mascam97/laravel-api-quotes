<?php

namespace App\Api\PublicQuotes\Queries;

use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PublicQuoteIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Quote::query()->select([
            'id',
            'title',
            'content',
            'state',
            'average_score',
            'user_id',
            'created_at',
            'updated_at',
        ])->whereState(Published::$name);

        parent::__construct($query, $request);

        $this->allowedFilters(['title', 'content', 'user_id'])
            ->allowedIncludes('user')
            ->allowedSorts('id', 'title', 'created_at');
    }
}
