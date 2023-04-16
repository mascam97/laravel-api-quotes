<?php

namespace App\Api\Ratings\Queries;

use Domain\Rating\Models\Rating;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class RatingShowQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Rating::query()->select([
            'id',
            'score',
            'qualifier_id',
            'qualifier_type',
            'rateable_id',
            'rateable_type',
            'created_at',
            'updated_at',
        ]);

        parent::__construct($query, $request);

        $this->allowedIncludes(['qualifier', 'rateable']);
    }
}
