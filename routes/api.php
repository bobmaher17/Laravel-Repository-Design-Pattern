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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//ARTICLE MODULE
Route::get('/articles', 'Articles\ArticlesController@index');
Route::post('/articles', 'Articles\ArticlesController@store');
Route::get('/articles/{id}', 'Articles\ArticlesController@show');
Route::post('/articles/{id}', 'Articles\ArticlesController@update');
Route::delete('/articles/{id}', 'Articles\ArticlesController@destroy');
