<?php

namespace App\ApiAnalytics\Quotes;

use Closure;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class QuotesQuery extends Query
{
    protected $attributes = [
        'name' => 'quotes',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Quote'));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
            ],
        ];
    }

    public function resolve(/* @phpstan-ignore-line */
        $root,
        array $args,
        User $context,
        ResolveInfo $resolveInfo,
        Closure $getSelectFields
    ): Collection|array {
        $columns = $getSelectFields()->getSelect();
        $relations = $getSelectFields()->getRelations();

        $conditions = function ($query) use ($args) {
            if (isset($args['id'])) {
                $query->where('id', $args['id']);
            }
        };

        return Quote::query()
            ->with($relations)
            ->where($conditions)
            ->select($columns)
            ->get();
    }
}
