<?php

namespace App\Providers;

use App\JsonApi\JsonApiBuilder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::mixin(new JsonApiBuilder);
    }
}
