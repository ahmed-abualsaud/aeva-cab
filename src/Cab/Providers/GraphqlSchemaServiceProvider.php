<?php

namespace Qruz\Cab\Providers;

use Event;

use Illuminate\Support\ServiceProvider;

use Nuwave\Lighthouse\Events\BuildSchemaString;
use Nuwave\Lighthouse\Schema\Source\SchemaStitcher;

class GraphqlSchemaServiceProvider extends ServiceProvider 
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $graphql = base_path('src/Cab/Application/Schemas/schema.graphql');
        if (file_exists($graphql)) {
            Event::listen(BuildSchemaString::class, function ($app) use ($graphql) {
                return (new SchemaStitcher($graphql))->getSchemaString();
            });
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}