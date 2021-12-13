<?php

/*
|--------------------------------------------------------------------------
| Mutations auth:none
|--------------------------------------------------------------------------
|
*/
Route::group(['namespace' => 'Mutations'], function () {
    Route::post('/admin/login', 'AdminController@login');
    Route::post('/driver/login', 'DriverController@login');
    
    Route::post('/user/create', 'UserController@create');
    Route::post('/user/login', 'UserController@login');
    Route::post('/user/social/login', 'UserController@socialLogin');

    Route::post('/seats/trip/terminal/transaction', 'SeatsTripTerminalTransactionController@create');

});
/*
|--------------------------------------------------------------------------
| Mutations auth:user
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => ['auth:user'], 'namespace' => 'Mutations'], function () {
    Route::post('/user/avatar/update', 'UserController@handleAvatar');
});

/*
|--------------------------------------------------------------------------
| Mutations auth:driver
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => ['auth:driver'], 'namespace' => 'Mutations'], function () {
    Route::post('/driver/avatar/update', 'DriverController@handleAvatar');
    Route::post('/driver/update', 'DriverController@update');
    Route::post('/driver/update/password', 'DriverController@updatePassword');
    Route::post('/business/trip/event/ready', 'BusinessTripEventController@ready');
    Route::post('/business/trip/event/start', 'BusinessTripEventController@start');
    Route::post('/business/trip/event/end', 'BusinessTripEventController@end');
    Route::post('/business/trip/event/at/station', 'BusinessTripEventController@atStation');
    Route::post('/business/trip/event/pick/users', 'BusinessTripEventController@pickUsers');
    Route::post('/business/trip/event/drop/users', 'BusinessTripEventController@dropUsers');
    Route::post('/business/trip/event/update/driver/location', 'BusinessTripEventController@updateDriverLocation');
    Route::post('/business/trip/event/change/attendance/status','BusinessTripEventController@changeAttendanceStatus');
    Route::post('/seats/trip/event/ready', 'SeatsTripEventController@ready');
    Route::post('/seats/trip/event/start', 'SeatsTripEventController@start');
    Route::post('/seats/trip/event/end', 'SeatsTripEventController@end');
    Route::post('/seats/trip/event/at/station', 'SeatsTripEventController@atStation');
    Route::post('/seats/trip/event/pick/users', 'SeatsTripEventController@pickUser');
    Route::post('/seats/trip/event/drop/users', 'SeatsTripEventController@dropUser');
    Route::post('/seats/trip/event/update/driver/location','SeatsTripEventController@updateDriverLocation');
    Route::post('/business/trip/attendance/create', 'BusinessTripAttendanceController@create');
    Route::post('/send/message', 'CommunicationController@sendBusinessTripChatMessage');
    Route::post('/seats/trip/booking/update', 'SeatsTripBookingController@update');
    Route::post('/seats/trip/pos/transaction', 'SeatsTripPosTransactionController@create');
});

/*
|--------------------------------------------------------------------------
| Queries auth:driver
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => ['auth:driver'], 'namespace' => 'Queries'], function () {
    Route::get('/driver/auth', 'DriverController@auth');
    Route::get('/driver/{id}', 'DriverController@show');
    Route::get('/business/trip/{id}', 'BusinessTripController@show');
    Route::get('/driver/{driver_id}/seats/trips', 'SeatsTripController@driverSeatsTrips');
    Route::get('/business/trip/{trip_id}/stations', 'BusinessTripController@businessTripStations');
    Route::get('/seats/trip/line/{line_id}/stations', 'SeatsTripController@seatsTripLineStations');
    Route::get('/driver/{driver_id}/business/trips/schedule','BusinessTripController@driverBusinessTripsSchedule');
    Route::get('/driver/{driver_id}/live/business/trips', 'BusinessTripController@driverLiveBusinessTrips');
    Route::get('/driver/{driver_id}/seats/trips/schedule', 'SeatsTripController@driverSeatsTripsSchedule');
    Route::get('/driver/{driver_id}/live/seats/trips', 'SeatsTripController@driverLiveSeatsTrips');
    Route::get('/seats/trip/{trip_id}/users', 'SeatsTripUserController@users');
    Route::get('/business/trip/{trip_id}/attendance', 'BusinessTripAttendanceController@businessTripAttendance');
    Route::get('/seats/trip/{trip_id}/app/transactions/detail' ,'SeatsTripController@seatsTripAppTransactionsDetail');
    Route::get('/user/{user_id}/business/trip/chat/messages', 'CommunicationController@businessTripChatMessages');
    Route::get('/business/trip/private/chat/users', 'CommunicationController@businessTripPrivateChatUsers');
    Route::get('/business/trip/{trip_id}/subscribers', 'BusinessTripSubscriptionController@businessTripSubscribers');
    Route::get('/business/trip/users/status/{trip_id?}', 'BusinessTripSubscriptionController@businessTripUsersStatus');
    Route::get('/business/trip/{trip_id}/user/{user_id}/status', 'BusinessTripSubscriptionController@businessTripUserStatus');
    Route::get('/partner/{partner_id}/payment-categories', 'PaymentCategoryController@partnerPaymentCategories');
    Route::get('/user/{user_id}/device/id', 'UserController@userDeviceId');
    Route::get('/driver/{driver_id}/device/id', 'DriverController@driverDeviceId');
    Route::get('/drivers/device/id', 'DriverController@driversDeviceId');
});

/*
|--------------------------------------------------------------------------
| Queries auth:admin,partner
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => ['auth:admin,partner'], 'namespace' => 'Queries'], function () {
    Route::get('/seats/trip/terminal/transactions/export', 'SeatsTripTerminalTransactionController@export');
    Route::get('/seats/trip/pos/transactions/export', 'SeatsTripPosTransactionController@export');
});