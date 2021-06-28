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
    Route::post('/user/avatar/update', 'UserController@handleAvatar');
});

Route::group(['middleware' => ['auth:driver']], function () {
    Route::post('/driver/avatar/update', 'DriverController@handleAvatar');
});

Route::post('/seats/trip/terminal/transaction', 'SeatsTripTerminalTransactionController@create');

Route::get('/seats/trip/terminal/transactions/export', 'SeatsTripTerminalTransactionController@export')
    ->middleware('auth:admin,partner');