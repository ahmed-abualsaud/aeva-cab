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

    'aevapay_staging_server_domain' => env('AEVAPAY_STAGING_SERVER_DOMAIN', 'staging.aevapay.net'),
    'aevapay_production_server_domain' => env('AEVAPAY_PRODUCTION_SERVER_DOMAIN', 'production.aevapay.net'),
    
    'aevapay_staging_server_key' => env('AEVAPAY_STAGING_SERVER_KEY', '$2y$10$PoO5Gfl4PAezsMeI0LPbKul5Kes4Ee06pIGGsMVV36Zy6BXne/Lom'),
    'aevapay_production_server_key' => env('AEVAPAY_PRODUCTION_SERVER_KEY', '$2y$10$raHvQpKOVZMJk/3s84KrbOQeze4fM3M0gQ57kaHUTVMkhJ4ibJF.i'),
    
    'aevacab_staging_server_key' => env('AEVACAB_STAGING_SERVER_KEY', '!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ'),
    'aevacab_production_server_key' => env('AEVACAB_PRODUCTION_SERVER_KEY', '!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ'),

    'credit_go_staging_server_domain'=> env('CREDIT_GO_STAGING_SERVER_DOMAIN','165.227.124.123'), 
    'credit_go_production_server_domain'=> env('CREDIT_GO_PRODUCTION_SERVER_DOMAIN','creditgo.app'), 
    
    'credit_go_staging_phone'=> env('CREDIT_GO_STAGING_SERVER_PHONE','01286308351'), 
    'credit_go_production_phone'=> env('CREDIT_GO_PRODUCTION_SERVER_PHONE','01126999840'), 
    
    'credit_go_staging_pass_code' => env('CREDIT_GO_STAGING_SERVER_PIN_CODE','000000'),
    'credit_go_production_pass_code' => env('CREDIT_GO_PRODUCTION_SERVER_PIN_CODE','246810'),

    'default_verification_code' => env('DEFAULT_VERIFICATION_CODE', 6254),

    'google_maps_url' => env('GOOGLE_MAPS_URL', 'https://maps.googleapis.com/maps/api/directions/json?key='.env('GOOGLE_MAP_KEY', 'AIzaSyDp1TeAL54m5bl6wCFIs4RZj4EPnvElR7g').'&sensor=false'),
];