<?php

namespace App\Api\Ratings\Queries;

use Domain\Rating\Models\Rating;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class RatingIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Rating::query();

        parent::__construct($query, $request);

        $this->allowedFilters(['qualifier_type', 'rateable_type'])
            ->allowedIncludes(['qualifier', 'rateable'])
            ->allowedSorts('id', 'created_at');
    }
}
