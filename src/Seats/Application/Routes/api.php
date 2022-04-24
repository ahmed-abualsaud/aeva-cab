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
        'namespace' => 'Aeva\Seats\Application\HTTP\Controllers\Mutations'
    ], function () {
        Route::post('/seats/trip/terminal/transaction', 'SeatsTripTerminalTransactionController@create');
});

/*
|--------------------------------------------------------------------------
| Mutations auth:driver
|--------------------------------------------------------------------------
|
*/
Route::group([
        'prefix' => 'rest',
        'middleware' => ['api', 'auth:driver'], 
        'namespace' => 'Aeva\Seats\Application\HTTP\Controllers\Mutations'
    ], function () {
        Route::post('/seats/trip/event/ready', 'SeatsTripEventController@ready');
        Route::post('/seats/trip/event/start', 'SeatsTripEventController@start');
        Route::post('/seats/trip/event/end', 'SeatsTripEventController@end');
        Route::post('/seats/trip/event/at/station', 'SeatsTripEventController@atStation');
        Route::post('/seats/trip/event/pick/users', 'SeatsTripEventController@pickUser');
        Route::post('/seats/trip/event/drop/users', 'SeatsTripEventController@dropUser');
        Route::post('/seats/trip/event/update/driver/location','SeatsTripEventController@updateDriverLocation');
        Route::post('/seats/trip/booking/update', 'SeatsTripBookingController@update');
        Route::post('/seats/trip/pos/transaction', 'SeatsTripPosTransactionController@create');
        Route::post('/seats/trip/pos/bulk-transaction', 'SeatsTripPosTransactionController@bulkCreate');
});

/*
|--------------------------------------------------------------------------
| Queries auth:driver
|--------------------------------------------------------------------------
|
*/
Route::group([
        'prefix' => 'rest',
        'middleware' => ['api', 'auth:driver'], 
        'namespace' => 'Aeva\Seats\Application\HTTP\Controllers\Queries'
    ], function () {
        Route::get('/driver/{driver_id}/seats/trips', 'SeatsTripController@driverSeatsTrips');
        Route::get('/seats/trip/line/{line_id}/stations', 'SeatsTripController@seatsTripLineStations');
        Route::get('/driver/{driver_id}/seats/trips/schedule', 'SeatsTripController@driverSeatsTripsSchedule');
        Route::get('/driver/{driver_id}/live/seats/trips', 'SeatsTripController@driverLiveSeatsTrips');
        Route::get('/seats/trip/{trip_id}/users', 'SeatsTripUserController@users');
        Route::get('/seats/trip/{trip_id}/app/transactions/detail' ,'SeatsTripController@seatsTripAppTransactionsDetail');
        Route::get('/seats/trip/pos/vehicle/{vehicle_id}/max-serial', 'SeatsTripPosTransactionController@vehicleMaxSerial');
});

/*
|--------------------------------------------------------------------------
| Queries auth:admin,partner
|--------------------------------------------------------------------------
|
*/
Route::group([
        'prefix' => 'rest',
        'middleware' => ['api', 'auth:admin,partner'], 
        'namespace' => 'Aeva\Seats\Application\HTTP\Controllers\Queries'
    ], function () {
        Route::get('/seats/trip/terminal/transactions/export', 'SeatsTripTerminalTransactionController@export');
        Route::get('/seats/trip/pos/transactions/export', 'SeatsTripPosTransactionController@export');
});

/*
|--------------------------------------------------------------------------
| Queries auth:manager
|--------------------------------------------------------------------------
|
*/
Route::group([
        'prefix' => 'rest',
        'middleware' => ['api', 'auth:manager'], 
        'namespace' => 'Aeva\Seats\Application\HTTP\Controllers\Queries'
    ], function () {
        Route::get('/seats/trip/driver/pos/transactions/report', 'SeatsTripPosTransactionController@driverReport');
});