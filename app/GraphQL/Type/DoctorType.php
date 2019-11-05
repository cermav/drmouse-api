<?php
/**
 * Created by PhpStorm.
 * User: burt
 * Date: 05.11.2019
 * Time: 16:19
 */

namespace App\GraphQL\Type;

use App\Doctor;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DoctorType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Doctor',
        'description'   => 'A doctor',
        'model'         => Doctor::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The doctor ID'
            ],
            'state_id' => [
                'type' => Type::int(),
                'description' => 'User state',
            ],
            // Uses the 'getIsMeAttribute' function on our custom User model
            'description' => [
                'type' => Type::string(),
                'description' => 'Doctor description'
            ]
        ];
    }
}
