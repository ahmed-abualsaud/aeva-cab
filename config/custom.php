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
    'db_backup_path' => env('DB_BACKUP_DIRECTORY', '/var/www/aeva/backup'),
    'telescope_token' => env('TELESCOPE_TOKEN'),

    'victorylink_username' => env('VICTORY_LINK_USERNAME'),
    'victorylink_password' => env('VICTORY_LINK_PASSWORD'),
    'victorylink_url' => env('VICTORY_LINK_URL'),
    'victorylink_sender' => env('VICTORY_LINK_SENDER'),

    'aevapay_staging_server_domain' => env('AEVAPAY_STAGING_SERVER_DOMAIN', 'staging.aevapay.net'),
    'aevapay_production_server_domain' => env('AEVAPAY_PRODUCTION_SERVER_DOMAIN', 'production.aevapay.net'),
    'aevapay_staging_server_key' => env('AEVAPAY_STAGING_SERVER_KEY', '$2y$10$PoO5Gfl4PAezsMeI0LPbKul5Kes4Ee06pIGGsMVV36Zy6BXne/Lom'),
    'aevacab_staging_server_key' => env('AEVACAB_STAGING_SERVER_KEY', '!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ'),
    'aevacab_production_server_key' => env('AEVACAB_STAGING_SERVER_KEY', '!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ'),
];