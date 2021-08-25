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
    Route::get('/driver/auth'       , 'DriverQueriesController@auth');
    Route::get('/driver/{id}'       , 'DriverQueriesController@driver');
    Route::get('/vehicle/{id}'      , 'DriverQueriesController@vehicle');
    Route::get('/supervisor/{id}'   , 'DriverQueriesController@supervisor');
    Route::get('/business/trip/{id}', 'DriverQueriesController@businessTrip');

    Route::get('/driver/{driver_id}/seats/trips'         , 'DriverQueriesController@driverSeatsTrips');
    Route::get('/business/trip/{trip_id}/stations'       , 'DriverQueriesController@businessTripStations');
    Route::get('/driver/{driver_id}/daily/business/trips', 'BusinessTripController@driverTrips');
    Route::get('/driver/{driver_id}/live/business/trips' , 'BusinessTripController@driverLiveTrips');
    Route::get('/driver/{driver_id}/daily/seats/trips'   , 'SeatsTripController@driverTrips');
    Route::get('/driver/{driver_id}/live/seats/trips'    , 'SeatsTripController@driverLiveTrips');
    
    Route::get('/seats/trip/{trip_id}/users'                   , 'SeatsTripUserController@users');
    Route::get('/business/trip/{trip_id}/attendance'           , 'BusinessTripAttendanceController@businessTripAttendance');
    Route::get('/seats/trip/{trip_id}/app/transactions/detail' , 'DriverQueriesController@seatsTripAppTransactionsDetail');
    Route::get('/user/{user_id}/business/trip/chat/messages'   , 'CommunicationController@businessTripChatMessages');
    Route::get('/business/trip/private/chat/users'             , 'CommunicationController@businessTripPrivateChatUsers');
    Route::get('/business/trip/{trip_id}/subscribers'          , 'BusinessTripSubscriptionController@businessTripSubscribers');
    Route::get('/business/trip/users/status/{trip_id?}'        , 'BusinessTripSubscriptionController@businessTripUsersStatus');
    Route::get('/business/trip/{trip_id}/user/{user_id}/status', 'BusinessTripSubscriptionController@businessTripUserStatus');
});