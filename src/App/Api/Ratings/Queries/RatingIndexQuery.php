<?php

namespace App\Api\Ratings\Queries;

use Domain\Rating\Models\Rating;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class RatingIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Rating::query()->select([
            'id',
            'score',
            'qualifier_id',
            'qualifier_type',
            'qualifier',
            'rateable_id',
            'rateable_type',
            'rateable',
            'created_at',
            'updated_at',
        ]);

        parent::__construct($query, $request);

        $this->allowedFilters(['qualifier_type', 'rateable_type'])
            ->allowedIncludes(['qualifier', 'rateable'])
            ->allowedSorts('id', 'created_at');
    }
}
