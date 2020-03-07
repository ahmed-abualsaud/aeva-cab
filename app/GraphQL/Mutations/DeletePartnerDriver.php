<?php

namespace App\GraphQL\Mutations;

use App\PartnerDriver;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Exceptions\CustomException;

class DeletePartnerDriver
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
      PartnerDriver::where('partner_id', $args['partner_id'])
      ->whereIn('driver_id', $args['driver_id'])
      ->delete();
    } catch (\Exception $e) {
      throw new CustomException(
        'Assignment cancellation faild.',
        'Something went wrong',
        'Unknown.'
      );
    }

    return [
      "status" => "SUCCESS",
      "message" => "Selected drivers have been unassigned successfully."
    ];

  }
}
