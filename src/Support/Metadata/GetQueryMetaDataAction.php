<?php

namespace Support\Metadata;

use Illuminate\Database\Query\Builder;

class GetQueryMetaDataAction
{
    public function __invoke(Builder $query): array
    {
        return [
            'current_sort' => $query->orders,
        ];
    }
}
