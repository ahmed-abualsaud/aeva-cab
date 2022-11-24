<?php

/*
|--------------------------------------------------------------------------
| Mutations auth:none
|--------------------------------------------------------------------------
|
*/
/*
Route::group([
        'prefix' => 'rest',
        'middleware' => ['api', 'auth:driver'],
        'namespace' => 'Aeva\Cab\Application\HTTP\Controllers\Mutations'
    ], function () {
        Route::post('/confirm/cashout', 'CabRequestTransactionController@confirmCashout');
});
*/
Route::group([
    'prefix' => 'rest',
    'namespace' => 'Aeva\Cab\Application\HTTP\Controllers\Queries'
], function () {
    Route::get('/user/{user_id}/live/cab/trip', 'CabRequestController@liveCabTrips');
});

