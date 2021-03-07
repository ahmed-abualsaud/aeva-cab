<?php

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

Route::group(['middleware' => ['auth:user']], function () {
    Route::group(['prefix' => 'user'], function () { 
        Route::post('/pay', 'PaymentController@pay');
        Route::post('/update/avatar', 'UserController@handleAvatar');
    });
});

Route::group(['middleware' => ['auth:driver']], function () {
    Route::post('/driver/avatar/update', 'DriverController@handleAvatar');
});