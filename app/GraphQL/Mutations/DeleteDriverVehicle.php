<?php

namespace App\GraphQL\Mutations;

use App\DriverVehicle;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Exceptions\CustomException; 

class DeleteDriverVehicle
{
  /**
   * Return a value for the field.
   *
   * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
   * @param  mixed[]  $args The arguments that were passed into the field.
   * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
   * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
   * @return mixed
   */
  public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
  {
    try {
      DriverVehicle::where('driver_id', $args['driver_id'])
        ->whereIn('vehicle_id', $args['vehicle_id'])
        ->delete();
    } catch (\Exception $e) {
      throw new CustomException(
        'Assignment cancellation faild.',
        'Something went wrong.',
        'Unknown.'
      );
    }
    return "Selected assignments have been cancelled successfully.";
  }
}
