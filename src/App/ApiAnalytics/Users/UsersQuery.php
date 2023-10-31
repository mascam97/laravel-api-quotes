<?php

namespace App\ApiAnalytics\Users;

use Closure;
use Domain\Users\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class UsersQuery extends Query
{
    protected $attributes = [
        'name' => 'users',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('User'));
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

        return User::query()
            ->with($relations)
            ->where($conditions)
            ->select($columns)
            ->get();
    }
}
