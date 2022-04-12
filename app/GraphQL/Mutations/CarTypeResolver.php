<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repository\Eloquent\Mutations\CarTypeRepository;

class CarTypeResolver 
{
    private $carTypeRepository;

    public function  __construct(CarTypeRepository $carTypeRepository)
    {
        $this->carTypeRepository = $carTypeRepository;
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $this->carTypeRepository->create($args);
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $this->carTypeRepository->update($args);
    }

    public function updateSurgeFactor($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $this->carTypeRepository->updateSurgeFactor($args);
    }
}