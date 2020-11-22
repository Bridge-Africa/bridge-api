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
Route::group([
    'middleware' => 'api',
    'namespace' => 'Api'
], function () {

    Route::group([
        'prefix' => 'auth'
    ], function () {

        Route::post('login', 'AuthController@login')->name('login');
        Route::post('register', 'AuthController@register')->name('register');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('me', 'AuthController@me');
        Route::post('language', 'AuthController@set_language');

    });
    Route::group([
        'prefix' => 'articles'
    ], function () {
        Route::get('/', 'ArticleController@index');
        Route::post('store', 'ArticleController@store');
        Route::get('{article}', 'ArticleController@getArticle');
        Route::post('{article}/update', 'ArticleController@update');
        Route::post('{article}/trash', 'ArticleController@trashArticle');
        Route::get('archived/all', 'ArticleController@getArchived');

    });
    Route::group([
        'prefix' => 'article_trash'
    ], function () {
        Route::post('{article}/restore', 'ArticleController@restoreArticle');
        Route::post('{article}/destroy', 'ArticleController@destroyArticle');
    });
});
