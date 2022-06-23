<?php

/*
|--------------------------------------------------------------------------
| Mutations auth:none
|--------------------------------------------------------------------------
|
*/
Route::group([
        'prefix' => 'rest',
        'middleware' => ['api'], 
        'namespace' => 'Aeva\Cab\Application\HTTP\Controllers\Mutations'
    ], function () {
        Route::post('/confirm/cashout', 'CabRequestTransactionController@confirmCashout');
});