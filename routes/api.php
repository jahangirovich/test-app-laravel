<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
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

// Route::post('/register', 'App\Http\Controllers\API\AuthController@register');
// Route::post('/login', 'App\Http\Controllers\API\AuthController@login');
// // Route::get('/login', 'App\Http\Controllers\API\AuthController@login')->name('login');

// Route::get('/', 'App\Http\Controllers\API\TaskController@index');
// Route::post('/create', 'App\Http\Controllers\API\TaskController@store');

Route::group(['middleware' => ['cors']], function () {
    Route::post('/register', 'App\Http\Controllers\API\AuthController@register');
    Route::post('/login', 'App\Http\Controllers\API\AuthController@login');
    // Route::get('/login', 'App\Http\Controllers\API\AuthController@login')->name('login');

    Route::get('/', 'App\Http\Controllers\API\TaskController@index');
    Route::post('/create', 'App\Http\Controllers\API\TaskController@store');

    Route::group(['middleware' => 'auth:api'], function(){
        Route::post('/edit/{id}', 'App\Http\Controllers\API\TaskController@update');
    });
});

