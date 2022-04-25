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
    'seats_search_radius' => env('SEATS_SEARCH_RADIUS'),
    'otp_signature' => env('OTP_SIGNATURE'),
    'db_name' => env('DB_DATABASE'),
    'db_user' => env('DB_USERNAME'),
    'db_pass' => env('DB_PASSWORD'),
    'db_backup_path' => env('DB_BACKUP_DIRECTORY', '/var/www/aeva/backup'),
    'telescope_token' => env('TELESCOPE_TOKEN'),

    'victorylink_username'=> env('VICTORY_LINK_USERNAME'),
    'victorylink_password'=> env('VICTORY_LINK_PASSWORD'),
    'victorylink_url'=> env('VICTORY_LINK_URL'),
    'victorylink_sender'=> env('VICTORY_LINK_SENDER'),
];