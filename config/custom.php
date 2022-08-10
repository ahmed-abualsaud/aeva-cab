<?php 

return [
    
    'app_url' => env('APP_URL'),
    'mail_to_address' => env('MAIL_TO_ADDRESS'),
    'otp_to_number' => env('OTP_TO_NUMBER'),
    'google_map_key' => env('GOOGLE_MAP_KEY'),
    'firebase_access_key' => env('FIREBASE_ACCESS_KEY'),
    'otp_username' => env('OTP_USERNAME'),
    'otp_password' => env('OTP_PASSWORD'),
    'otp_sender_id' => env('OTP_SENDER_ID'),
    'otp_signature' => env('OTP_SIGNATURE'),
    'db_name' => env('DB_DATABASE'),
    'db_user' => env('DB_USERNAME'),
    'db_pass' => env('DB_PASSWORD'),
    'db_host' => env('DB_HOST'),
    'db_backup_path' => env('DB_BACKUP_DIRECTORY', '/var/www/aeva/backup'),
    'telescope_token' => env('TELESCOPE_TOKEN'),

    'victorylink_username' => env('VICTORY_LINK_USERNAME'),
    'victorylink_password' => env('VICTORY_LINK_PASSWORD'),
    'victorylink_url' => env('VICTORY_LINK_URL'),
    'victorylink_sender' => env('VICTORY_LINK_SENDER'),
    
    'aevacab_staging_server_key' => env('AEVACAB_STAGING_SERVER_KEY', '!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ'),
    'aevacab_production_server_key' => env('AEVACAB_PRODUCTION_SERVER_KEY', '!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ'),

    'default_verification_code' => env('DEFAULT_VERIFICATION_CODE', 6254),

    'google_maps_url' => env('GOOGLE_MAPS_URL', 'https://maps.googleapis.com/maps/api/directions/json?key='.env('GOOGLE_MAP_KEY', 'AIzaSyDp1TeAL54m5bl6wCFIs4RZj4EPnvElR7g').'&sensor=false'),

    // ================================================ Environment Credentials ================================================

    'send_otp' => env('SEND_OTP'),
    'queue_high' => env('QUEUE_HIGH'),
    'websocket_host' => env('WEBSOCKET_HOST'),

    'aevapay_server_url' => env('AEVAPAY_SERVER_URL'),
    'aevapay_server_key' => env('AEVAPAY_SERVER_KEY'),
    'aevapay_slug_pay' => env('AEVAPAY_SLUG_PAY'),

    'credit_go_phone' => env('CREDIT_GO_PHONE'),
    'credit_go_pass_code' => env('CREDIT_GO_PASS_CODE'),
    'credit_go_server_url' => env('CREDIT_GO_SERVER_URL'),
    'credit_go_slug_auth' => env('CREDIT_GO_SLUG_AUTH'),
    'credit_go_slug_cashout' => env('CREDIT_GO_SLUG_CASHOUT'),
];