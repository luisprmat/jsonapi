<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
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
        $api->hasOne('categories');
    });

    $api->resource('authors')->only('index', 'read')->relationships(function ($api) {
        $api->hasMany('articles')->except('replace', 'add', 'remove');
    });

    $api->resource('categories')->relationships(function ($api) {
        $api->hasMany('articles')->except('replace', 'add', 'remove');
    });

    Route::post('login', [LoginController::class, 'login'])->name('login')
        ->middleware('guest:sanctum');

    Route::post('logout', [LoginController::class, 'logout'])->name('logout')
        ->middleware('auth:sanctum');
});
