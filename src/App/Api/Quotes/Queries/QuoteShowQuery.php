<?php

namespace App\Api\Quotes\Queries;

use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class QuoteShowQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $quoteId = $request->route('quoteId');

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
            ])
            ->whereState(Published::$name);

        parent::__construct($query, $request);

        $this->allowedIncludes('user');
    }
}
