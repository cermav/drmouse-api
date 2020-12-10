<?php

namespace App\GraphQL\Query;

use App\Models\Doctor;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class DoctorsQuery extends Query
{
    protected $attributes = [
        'name' => 'Doctors Query',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('doctor'));
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'description' => [
                'name' => 'description',
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve(
        $root,
        $args,
        $context,
        ResolveInfo $resolveInfo,
        \Closure $getSelectFields
    ) {
        if (isset($args['id'])) {
            return Doctor::where('id', $args['id'])->get();
        }

        if (isset($args['description'])) {
            return Doctor::where('description', $args['description'])->get();
        }

        return Doctor::all();
    }
}
