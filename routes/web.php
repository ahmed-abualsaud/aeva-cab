<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('{type}/password/reset/{token}?email={email}', 'Auth\ResetPasswordController@showResetForm')
    ->name('password.reset');

Route::get('pay', 'PaymentController@view');