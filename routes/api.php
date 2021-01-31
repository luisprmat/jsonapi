<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

JsonApi::register('v1')->routes(function($api){
    $api->resource('articles')->relationships(function ($api) {
        $api->hasOne('authors');
    });
    $api->resource('authors')->only('index', 'read');
});
