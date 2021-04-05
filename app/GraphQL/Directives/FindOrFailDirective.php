<?php

namespace App\GraphQL\Directives;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Model;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\FieldResolver;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FindOrFailDirective extends BaseDirective implements FieldResolver
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
            """
            Find a model or throw an exception based on the arguments provided.
            """
            directive @findOrFail(
            """
            Specify the class name of the model to use.
            This is only needed when the default model detection does not work.
            """
            model: String
            """
            Apply scopes to the underlying query.
            """
            scopes: [String!]
            ) on FIELD_DEFINITION
            GRAPHQL;
    }

    public function resolveField(FieldValue $fieldValue): FieldValue
    {
        return $fieldValue->setResolver(
            function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?Model {
                try {
                    $results = $resolveInfo
                        ->argumentSet
                        ->enhanceBuilder(
                            $this->getModelClass()::query(),
                            $this->directiveArgValue('scopes', [])
                        )->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    throw new CustomException($e->getMessage(), "modelNotFound");
                }
                return $results;
            }
        );
    }
}