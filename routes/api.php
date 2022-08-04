<?php

use App\Http\Controllers\Queries\Exports\ExportCabRequestsController;
use App\Http\Controllers\Queries\Exports\ExportDriversController;
use App\Http\Controllers\Queries\Exports\ExportDriverTransactionsController;
use App\Http\Controllers\Queries\PartnerController;
use Illuminate\Support\Facades\Route;

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
    Route::post('/upload/driver/document', 'DocumentController@uploadDocument');
    Route::post('/business/trip/event/ready', 'BusinessTripEventController@ready');
    Route::post('/business/trip/event/start', 'BusinessTripEventController@start');
    Route::post('/business/trip/event/end', 'BusinessTripEventController@end');
    Route::post('/business/trip/event/at/station', 'BusinessTripEventController@atStation');
    Route::post('/business/trip/event/pick/users', 'BusinessTripEventController@pickUsers');
    Route::post('/business/trip/event/drop/users', 'BusinessTripEventController@dropUsers');
    Route::post('/business/trip/event/update/driver/location', 'BusinessTripEventController@updateDriverLocation');
    Route::post('/business/trip/event/change/attendance/status','BusinessTripEventController@changeAttendanceStatus');
    Route::post('/business/trip/attendance/create', 'BusinessTripAttendanceController@create');
    Route::post('/send/message', 'CommunicationController@sendBusinessTripChatMessage');
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
    Route::get('/business/trip/{trip_id}/stations', 'BusinessTripController@businessTripStations');
    Route::get('/driver/{driver_id}/business/trips/schedule','BusinessTripController@driverBusinessTripsSchedule');
    Route::get('/driver/{driver_id}/live/business/trips', 'BusinessTripController@driverLiveBusinessTrips');
    Route::get('/business/trip/{trip_id}/attendance', 'BusinessTripAttendanceController@businessTripAttendance');
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

Route::get('/driver/by/phone/{phone}', 'Queries\DriverController@getByPhone');

Route::group([
    'prefix' => 'drivers',
    'middleware' => ['auth:admin'],
    'as' => 'drivers.'
    ], function () {

    Route::get('export', [ExportDriversController::class,'__invoke']);
});

Route::group([
    'prefix' => 'driver-transactions',
    'middleware' => ['auth:admin'],
    'as' => 'driver.transactions.'
    ], function () {

    Route::get('export', [ExportDriverTransactionsController::class,'__invoke']);
});

Route::group([
    'prefix' => 'cab-requests',
    'middleware' => ['auth:admin'],
    'as' => 'cab.requests.'
    ], function () {

    Route::get('export', [ExportCabRequestsController::class,'__invoke']);
});

Route::group([
    'prefix' => 'partners',
    'as' => 'partners.'
    ], function () {

    Route::get('/', [PartnerController::class,'index']);
    Route::get('cash-out', [PartnerController::class,'cashOut']);
});
