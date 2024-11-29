<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function() {
    Route::get('auth/test','App\Http\Controllers\api\AuthController@testToken');

    Route::post('todo','App\Http\Controllers\api\TodoController@create');
    Route::get('todo','App\Http\Controllers\api\TodoController@read');
    Route::patch('todo/{id}','App\Http\Controllers\api\TodoController@update');

    Route::post('progress','App\Http\Controllers\api\ProgressController@create');
    Route::get('progress','App\Http\Controllers\api\ProgressController@read');
    Route::patch('progress/{id}','App\Http\Controllers\api\ProgressController@update');
});

Route::post('/auth/register','App\Http\Controllers\api\AuthController@register');
Route::post('/auth/login','App\Http\Controllers\api\AuthController@login');
Route::post('/auth/refresh','App\Http\Controllers\api\AuthController@refreshToken');