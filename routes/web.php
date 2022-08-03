<?php
use Illuminate\Support\Facades\DB;

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

Route::get('/', 'WelcomeController@index');

Route::get('{type}/password/reset/{token}?email={email}', 'Auth\ResetPasswordController@showResetForm')
    ->name('password.reset');

Route::get('/test', function() {

    return App\PromoCodeUsage::selectRaw('
        promo_codes.id,
        promo_codes.name,
        promo_codes.max_trips,
        COUNT(promo_codes.id) as count
    ')
    ->join('promo_codes', 'promo_code_usages.promo_code_id', 'promo_codes.id')
    ->where('user_id', 4660)
    ->where('expires_on', '>', date('Y-m-d'))
    ->where('promo_code_usages.used', true)
    ->groupBy('promo_codes.id')
    ->havingRaw('count < max_trips')
    ->first();

    return empty(1);
    // $s_lat = 31.297919875584697;
    // $s_lng = 30.068807232026757;
    // $d_lat = 31.29610207596253;
    // $d_lng = 30.066010210258643;
    // $distance = 0;
    //     $duration = 0;
    //     return $response = Illuminate\Support\Facades\Http::get(config('custom.google_maps_url').'&%2520waypoints=optimize:true%7C&origin='.$s_lat.','.$s_lng.'&destination='.$d_lat.','.$d_lng)->json();
    //     if ($response['status'] ==  'OK') {
    //         foreach ($response['routes'][0]['legs'] as $leg) {
    //             $distance += $leg['distance']['value'];
    //             $duration += $leg['duration']['value'];
    //         }
    //     }
    //     return ['distance' => $distance, 'duration' => $duration];

    $locations = [
        [
            'id' => 1,
            'latitude' => 31.287631537993608,
            'longitude' => 30.046218515912383
        ],
        [
            'id' => 3,
            'latitude' => 31.287586190339592,
            'longitude' => 30.046161913455386
        ],
        [
            'id' => 2,
            'latitude' => 31.287549912200653,
            'longitude' => 30.046082316250235
        ],
        [
            'id' => 5,
            'latitude' => 31.28749625076125,
            'longitude' => 30.046002719045084
        ],
        [
            'id' => 4,
            'latitude' => 31.287462239973976,
            'longitude' => 30.045938156867567
        ],
        [
            'id' => 6,
            'latitude' => 31.28741689223851,
            'longitude' => 30.045870057036492
        ],
        [
            'id' => 7,
            'latitude' => 31.28737985823839,
            'longitude' => 30.045803726032197
        ]
    ];
    $ret[] = Aeva\Cab\Domain\Helpers\S2Helper::toCellIDs($locations, 19);

    $locations = [
        [
            'id' => 8,
            'latitude' => 31.281496801324213,
            'longitude' => 30.022687890612126
        ],
        [
            'id' => 1,
            'latitude' => 31.28158397668935,
            'longitude' => 30.0226555478538
        ],
        [
            'id' => 3,
            'latitude' => 31.281682846213265,
            'longitude' => 30.022623205095474
        ],
        [
            'id' => 2,
            'latitude' => 31.281767895183172,
            'longitude' => 30.022593350241635
        ],
        [
            'id' => 5,
            'latitude' => 31.281800851638383,
            'longitude' => 30.022710281752506
        ],
        [
            'id' => 4,
            'latitude' => 31.28182849252743, 
            'longitude' => 30.022813529788703
        ],
        [
            'id' => 6,
            'latitude' => 31.281872080066762,
            'longitude' => 30.02298892705502
        ],
        // [
        //     'id' => 7,
        //     'latitude' => 31.28499129846048, 
        //     'longitude' => 30.029660830973967
        // ],
        // [
        //     'id' => 9,
        //     'latitude' => 31.285132841833082, 
        //     'longitude' => 30.02964741991849
        // ]
    ];
    $ret[] = Aeva\Cab\Domain\Helpers\S2Helper::toCellIDs($locations, 19);

    $locations = [
        [
            'id' => 1,
            'latitude' => 31.28474889777976,
            'longitude' => 30.02934768309936
        ],
        [
            'id' => 3,
            'latitude' => 31.284827978921392,
            'longitude' => 30.02933762481645
        ],
        [
            'id' => 2,
            'latitude' => 31.284774685114925,
            'longitude' => 30.029463688617817
        ],
        [
            'id' => 5,
            'latitude' => 31.284738582842444,
            'longitude' => 30.029296050569307
        ],
        [
            'id' => 4,
            'latitude' => 31.284939150847983, 
            'longitude' => 30.029354388610187
        ],
        [
            'id' => 6,
            'latitude' => 31.2848806996447,
            'longitude' => 30.029149870191016
        ],
        [
            'id' => 7,
            'latitude' => 31.284827978921392,
            'longitude' => 30.02933762481645
        ]
    ];
    $ret[] = Aeva\Cab\Domain\Helpers\S2Helper::toCellIDs($locations, 19);
    return $ret;
    $locations = json_decode(json_encode($locations));
    usort($locations, fn($a, $b) =>  $a->id > $b->id);

    foreach ($locations as $location) {
        $path[] = $location->latitude.','.$location->longitude;
    }
    $locations = implode('|', $path);

    // $s_lat = 31.29215902569163;
    // $s_lng = 30.053245517549033;
    // $d_lat = 31.274271104918338;
    // $d_lng = 30.001410232964737;

    //$locations = '31.29215902569163,30.053245517549033|31.274271104918338,30.001410232964737';

    //$locations = '31.28359815852212,30.029511040499113|31.27028618332969,30.031262856831106';

    $s_lat = 31.297919875584697;
    $s_lng = 30.068807232026757;
    $d_lat = 31.29610207596253;
    $d_lng = 30.066010210258643;

    $locations = '31.29813775760492,30.068715152731134|31.29748108388072,30.067932478633526|31.297644496203944,30.06923575481771';

    $distance = 0;
    $duration = 0;
    $response = Illuminate\Support\Facades\Http::get(config('custom.google_maps_url').'&waypoints='.$locations.'&origin='.$s_lat.','.$s_lng.'&destination='.$d_lat.','.$d_lng);
    if ($response['status'] ==  'OK') {
        foreach ($response['routes'][0]['legs'] as $leg) {
            $distance += $leg['distance']['value'];
            $duration += $leg['duration']['value'];
        }
    }
    return ['distance' => $distance, 'duration' => $duration];

    // return App\Helpers\FirebasePushNotification::push(
    //     'fJSu6wpmRcGJ3K6REE9HCT:APA91bG46k89NHOuESN0D2Uq3NepRBuZ9AbbrFvamei4ybCbdmwVVHmm4OxbmRrRiJjE4j_KObCaQ7l4Uxam9bi6wUJ7_WQaxrTtwIP4cq7agVRLfrZy2f_a0U6S38Tc4PSz3qxYgiRG',
    //     __('lang.ride_redirection_body'),
    //     __('lang.ride_redirection'),
    //     ['view' => 'RideRedirection', 'id' => 1]
    // );

    // $response = Illuminate\Support\Facades\Http::get(config('custom.google_maps_url').'&%2520waypoints=optimize:true%7C&origin='.$s_lat.','.$s_lng.'&destination='.$d_lat.','.$d_lng);
    // if ($response['status'] ==  'OK') {
    //     foreach ($response['routes'][0]['legs'] as $leg) {
    //         $distance = $leg['distance']['value'];
    //         $duration = $leg['duration']['value'];
    //     }
    //     return [
    //         'distance' => $distance,
    //         'duration' => $duration
    //     ];
    // }

    //return ['distance' => 0, 'duration' => 0];
    //return App\DriverStats::where('driver_id', 21)->increment('accepted_cab_requests', 1);
    
    // $promo_code =  App\PromoCodeUsage::selectRaw('
    //     promo_codes.id, 
    //     promo_codes.name, 
    //     COUNT(promo_codes.id) as count
    // ')
    // ->join('promo_codes', 'promo_code_usages.promo_code_id', 'promo_codes.id')
    // ->where('user_id', 4663)
    // ->groupBy('promo_codes.name', 'promo_codes.id')
    // ->having('count', '<', 4)
    // ->first();

    // return $promo_code;

    //  ini_set('post_max_size', '1M');
    //  return ini_get_all();
//01004896260
    //return App\Helpers\Otp::send('01126999840', 'hello');
    //return App\Driver::with('stats')->where('phone', '01004896260')->get();
    //return App\Vehicle::where('approved', true)->get();
    //return App\User::where('phone', '+201006924808')->get();
    // Illuminate\Support\Facades\Log::info('hi');
    // return 1;
    $reqs =  Aeva\Cab\Domain\Models\CabRequest::where('history->missing->status', true)->get();
    foreach ($reqs as $req) {
        $req->missed_drivers = App\Driver::whereIn('id', $req->history['missing']['missed'])->get();
    }
    return $reqs;
            //->whereIn('status', ['Searching', 'Sending', 'Accepted', 'Arrived', 'Started', 'Ended'])
            //->update(['status' => 'Completed']);
            //->get();
    //return App\User::where('phone', '+201270224224')->get();

    // return config('custom.aevapay_production_server_domain');
    // return config('custom.aevapay_staging_server_domain');
    // return config('custom.aevapay_staging_server_key');
    // return config('custom.aevacab_staging_server_key');
    // return Aeva\Cab\Domain\Models\CabRequest::where('phone', '4655')
    //     ->update(['status' => 'Completed']);

    // return Aeva\Cab\Domain\Models\CabRequest::where('driver_id', 28)
    //     ->whereIn('status', ['Searching', 'Sending', 'Accepted', 'Arrived', 'Started', 'Ended'])
    //     ->get();

    //$id = App\User::where('phone', '+201147677679')->first()->id;
    // return Aeva\Cab\Domain\Models\CabRequest::where('user_id', 4663)
    // ->whereNotIn('status', ['Completed', 'Cancelled'])
    // ->update(['status' => 'Completed']);

    // $radius = App\Settings::select('name', 'value')->where('name', 'Coverage Radius')->first()->value;
    // return $radius / 5;

    $str = config('custom.aevacab_staging_server_key').'46460';
    $hashed_str = hash("sha256",$str,true);
    return $encoded_str = base64_encode($hashed_str);


    $trx = new Aeva\Cab\Domain\Models\CabRequestTransaction();
    $trx_rpo = new Aeva\Cab\Domain\Repository\Eloquent\Mutations\CabRequestTransactionRepository($trx);
    return $trx_rpo->pay([
        'user_id' => 4660,
        'amount' => 10,
        'type' => 'Aevapay User Wallet',
        'uuid' => Illuminate\Support\Str::orderedUuid()
    ]);
});