<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MovieController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::resource('projects', MovieController::class)->middleware('auth:api');

Route::group([
    'namespace' => '\App\Http\Controllers\Api',
    'middleware' => ['cors'],
    'prefix' => 'v1'
], function ($router) {
    $router->post('/login', 'UserController@login');
    $router->get('/logout', function () {
        return null;
    });
});

Route::group([
    'namespace' => '\App\Http\Controllers\API',
    'middleware' => ['auth:api'],
    'prefix' => 'v1'
], function ($router) {
    // List movie
    $router->post('/event/submit', 'MovieController@submit');
    $router->post('/event/list', 'MovieController@list');
});