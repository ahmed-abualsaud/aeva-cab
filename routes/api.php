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


# ------------------------------- Driver App Mutations ---------------------------------

Route::post('/driver/login', 'DriverApp\Mutations\DriverController@login');

Route::group(['middleware' => ['auth:driver'], 'namespace' => 'DriverApp\Mutations'], function () {
    Route::post('/driver/update'                 , 'DriverController@update');
    Route::post('/driver/update/password'        , 'DriverController@updatePassword');
    Route::post('/business/trip/event/ready'     , 'BusinessTripEventController@ready');
    Route::post('/business/trip/event/start'     , 'BusinessTripEventController@start');
    Route::post('/business/trip/event/end'       , 'BusinessTripEventController@end');
    Route::post('/business/trip/event/at/station', 'BusinessTripEventController@atStation');
    Route::post('/business/trip/event/pick/users', 'BusinessTripEventController@pickUsers');
    Route::post('/business/trip/event/drop/users', 'BusinessTripEventController@dropUsers');

    Route::post('/business/trip/event/update/driver/location'  , 'BusinessTripEventController@updateDriverLocation');
    Route::post('/business/trip/event/change/attendance/status', 'BusinessTripEventController@changeAttendanceStatus');
    
    Route::post('/seats/trip/event/ready'        , 'SeatsTripEventController@ready');
    Route::post('/seats/trip/event/start'        , 'SeatsTripEventController@start');
    Route::post('/seats/trip/event/end'          , 'SeatsTripEventController@end');
    Route::post('/seats/trip/event/at/station'   , 'SeatsTripEventController@atStation');
    Route::post('/seats/trip/event/pick/users'   , 'SeatsTripEventController@pickUsers');
    Route::post('/seats/trip/event/drop/users'   , 'SeatsTripEventController@dropUsers');
    
    Route::post('/seats/trip/event/update/driver/location', 'SeatsTripEventController@updateDriverLocation');
    Route::post('/business/trip/attendance/create'        , 'BusinessTripAttendanceController@create');
    
    Route::post('/send/message'                  , 'CommunicationController@sendBusinessTripChatMessage');
    Route::post('/seats/trip/booking/update'     , 'SeatsTripBookingController@update');
});

# ------------------------------- Driver App Queries ---------------------------------

Route::group(['middleware' => ['auth:driver'], 'namespace' => 'DriverApp\Queries'], function () {
    Route::get('/driver/auth'        , 'DriverQueriesController@auth');
    Route::get('/driver/{id}'        , 'DriverQueriesController@driver');
    Route::get('/vehicle/{id}'       , 'DriverQueriesController@vehicle');
    Route::get('/supervisor/{id}'    , 'DriverQueriesController@supervisor');
    Route::get('/business/trip/{id}' , 'DriverQueriesController@businessTrip');

    Route::get('/driver/seats/trips/{driver_id}'          , 'DriverQueriesController@driverSeatsTrips');
    Route::get('/business/trip/stations/{trip_id}'        , 'DriverQueriesController@businessTripStations');
    Route::get('/driver/business/trips/{driver_id}/{day?}', 'BusinessTripController@driverTrips');
    Route::get('/driver/business/trips/live/{driver_id}'  , 'BusinessTripController@driverLiveTrips');
    Route::get('/driver/seats/trips/{driver_id}/{day}'    , 'SeatsTripController@driverTrips');
    Route::get('/driver/seats/trips/live/{driver_id}'     , 'SeatsTripController@driverLiveTrips');
    
    Route::get('/seats/trip/users/{trip_id}/{trip_time}/{status}/{station_id?}', 'SeatsTripUserController@users');
    Route::get('/business/trip/attendance/{trip_id}/{date?}'                   , 'BusinessTripAttendanceController@businessTripAttendance');
    Route::get('/seats/trip/app/transactions/detail/{trip_time}/{trip_id}'     , 'DriverQueriesController@seatsTripAppTransactionsDetail');
    Route::get('/business/trip/chat/messages/{log_id}/{user_id}/{is_private}'  , 'CommunicationController@businessTripChatMessages');
    Route::get('/business/trip/private/chat/users/{log_id}'                    , 'CommunicationController@businessTripPrivateChatUsers');
    Route::get('/business/trip/subscribers/{trip_id}/{status}/{station_id?}'   , 'BusinessTripSubscriptionController@businessTripSubscribers');
    Route::get('/business/trip/users/status/{trip_id?}/{station_id?}'          , 'BusinessTripSubscriptionController@businessTripUsersStatus');
    Route::get('/business/trip/user/status/{trip_id}/{user_id}'                , 'BusinessTripSubscriptionController@businessTripUserStatus');
});