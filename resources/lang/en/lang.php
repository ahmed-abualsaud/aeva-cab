<?php

return [

    'admin_not_found'   => 'The provided admin ID is not found',
    'invalid_auth_credentials' => 'The provided authentication credentials are invalid',
    'verification_code' => 'Your Aeva Cab code is: :verification_code %0a :signature',
    'password_missmatch' => 'Your current password does not matches with the password you provided',
    'type_new_password' => 'New Password cannot be same as your current password. Please choose a different password',
    'password_changed' => 'Password changed successfully',
    'password_not_changed' => 'Can\'t reset your password',
    'create_trnx_failed' => 'Could not create transaction',
    'create_attendance_failed' => 'Could not create or update an attendance record',
    'trip_already_started' => 'This Trip has already been started',
    'trip_started' => 'has been started',
    'driver_not_ready' => 'Driver is not ready',
    'captain_arrived' => 'Aeva Cab captain has arrived at your station and will leave after 1 min',
    'notify_station_failed' => 'Could not notify selected station\'s users',
    'welcome_trip' => 'Welcome! May you be happy and safe throughout this trip',
    'bye_trip' => 'Bye! We can\'t wait to see you next time',
    'trip_ended' => 'This trip has already been ended',
    'change_user_status_failed' => 'Could not change selected users status',
    'attendence_changed' => ':user has changed his attendance status to :status',
    'captain_changed_attendance' => 'The trip captain has changed your attendance status to :status, If this isn\'t the case, you could revert it back from inside the trip',
    'create_trip_failed' => 'Could not create this trip',
    'add_request_failed' => 'Could not add selected requests! kindly make sure that none of the selected requests has already subscribed to the selected trip',
    'assign_user_station_failed' => 'Could not assign users to specified station',
    'trip_not_found' => 'Trip with the provided ID is not found',
    'update_route_failed' => 'Could not update route',
    'copy_trip_failed' => 'Could not copy this trip',
    'invite_user_failed' => 'Could not invite selected users',
    'user_invited_not_verified' => 'Selected users have been invited but still not verified',
    'subscribe_user_failed' => 'Could not subscribe selected users',
    'subscribe_user' => 'Selected users have been subscribed',
    'already_subscribed' => 'You have already subscribed to this trip',
    'cancel_subscribe_failed' => 'Subscription cancellation failed',
    'toggle_subscribe_failed' => 'Could not toggle this subscription',
    'subscription_code' => 'Dear valued user, kindly use this code to confirm your subscription for :trip_name trip = :subscription_code',
    'create_schedule_failed' => 'Could not update or even create this schedule',
    'station_not_found' => 'The provided station ID is not found',
    'something_went_wrong' => 'Something went wrong! please try again',
    'accept_station_failed' => 'Could not accept this station',
    'car_model_not_found' => 'The provided carModel ID is not found',
    'car_type_not_found' => 'The provided carType ID is not found',
    'message_sent' => 'Message sent successfully',
    'send_message_failed' => 'Could not send message to selected recipients',
    'save_message_failed' => 'Could not save this message',
    'document_not_found' => 'Document with the provided ID is not found. ',
    'document_deleted' => 'Document has been deleted',
    'driver_not_found' => 'The provided driver ID is not found',
    'assignment_failed' => 'Assignment faild',
    'assign_vehicle' => 'Selected vehicles have been assigned successfully',
    'assign_cancel_failed' => 'Assignment cancellation faild',
    'unassign_vehicle' => 'Selected vehicles have been unassigned successfully',
    'fleet_not_found' => 'The provided fleet ID is not found',
    'create_request_failed' => 'Could not create this request',
    'change_request_failed' => 'Request status can not be changed',
    'cancel_request_failed' => 'This request can not be cancelled',
    'request_submitted' => 'New On-Demand request has been submitted',
    'request_ID_submitted' => 'New On-Demand request # :request_id has been submitted',
    'partner_not_found' => 'The provided partner ID is not found',
    'manager_not_found' => 'The provided manager ID is not found',
    'driver_assign_failed' => 'Driver can not be assigned to the same partner more than once',
    'driver_assigned' => 'Selected drivers have been assigned successfully',
    'driver_unassigned' => 'Selected drivers have been unassigned successfully',
    'user_assign_failed' => 'User can not be assigned to the same partner more than once',
    'user_assigned' => 'Selected users have been assigned successfully',
    'user_unassigned' => 'Selected users have been unassigned successfully',
    'add_card_failed' => 'Could not add this card',
    'card_added' => 'Payment card added successfully',
    'resend_code_failed' => 'Could not resend the code',
    'code_resent' => 'Validation code resent successfully',
    'validate_OTP_failed' => 'Could not validate the OTP',
    'OTP_validated' => 'OTP validated successfully',
    'process_payment_failed' => 'Could not process this payment',
    'card_not_found' => 'Card not found',
    'create_price_failed' => 'Could not create this price package',
    'update_price_failed' => 'Could not update this price package',
    'invalid_promo_code' => 'Invalid or expired promo code',
    'permitted_usage_exceeded' => 'You have exceeded the permitted usage times',
    'role_not_found' => 'The provided role ID is not found',
    'create_school_request_failed' => 'Could not create this school request',
    'update_school_request_failed' => 'Could not update this school request',
    'request_changed' => 'selected requests status has been changed',
    'delete_request_failed' => 'Could not delete selected requests',
    'request_deleted' => 'Selected requests have been deleted',
    'copy_line_failed' => 'Could not copy this line',
    'update_booking_failed' => 'Could not update this booking',
    'no_seats' => 'No available seats',
    'available_seats' => 'Only :available_seats :pluralSeats available',
    'create_booking_failed' => 'Could not create this booking',
    'update_wallet_failed' => 'Could not update the wallet',
    'drop_user_failed' => 'Could not drop off user',
    'password_phone_not_provided' => 'Password or phone is required but not provided',
    'create_user_failed' => 'Could not create users',
    'user_not_found' => 'The provided user ID is not found',
    'invalid_token' => 'The provided token is invalid',
    'vehicle_not_found' => 'The provided vehicle ID is not found',
    'create_workplace_failed' => 'Could not create this workplace request',
    'update_workplace_failed' => 'Could not update this workplace request',
    'change_requests_failed' => 'Could not change selected requests status',
    'no_schedule' => 'There is no schedule for this user at this trip',
    'get_user_status_failed' => 'Could not get the user status at this trip',
    'no_chat_messages' => 'Could not find this chat messages',
    'not_available_name' => 'The chosen name is not available',
    'terminal_exist' => 'This terminal already exists',
    'payment_category_exist' => 'This category already exists',
    'device_exist' => 'This device already exists',
    'not_available_phone' => 'The chosen phone is not available',
    'not_available_email' => 'The chosen email is not available',
    'not_available_national_id' => 'The chosen nationa id is not available',
    'not_available_secondary_phone' => 'The chosen secondary phone is not available',
    'not_available_arabic_name' => 'The chosen arabic name is not available',
    'not_available_type' => 'The chosen type is not available',
    'not_available_license' => 'The chosen license plate is not available',
    'set_language_failed' => 'Could not configure App language',
    'upload_file_failed' => 'Could not upload this file',
    'update_failed' => 'Could not update',
    'insufficient_balance' => 'Your wallet balance is insufficient',
    'supervisor_not_found' => 'Supervisor not found',
    'rating_not_found' => 'Rating not found',
    'trip_schedule_required' => 'Trip schedule required',
    'the_following_already_exists' => 'The following already exists',
    'user_exists' => 'User already exists',
    'you_already_booked_the_trip' => 'You already booked the trip',
    'your_account_is_disabled' => 'Your account has been blocked',
    'permitted_number_of_trips_exceeded' => 'Permitted number of trips exceeded',
    'permitted_number_of_users_exceeded' => 'Permitted number of users exceeded',
    'promocode_has_already_been_applied' => 'Promocode has already been applied',
    'payment_method_does_not_match' => 'The applied payment method does not match the requested payment method',
    'amount_paid_less_than_amount_requested' => 'The amount paid is less than the amount requested',
    'you_already_applyed_another_promo_code' => 'You already applied another promocode',
    'empty_user_wallet' => 'The user\'s wallet is empty. Please pay in cash',
    'insufficient_driver_wallet_balance' => 'Insufficient driver wallet balance, The maximum amount of cash you can take is :cash_amount EGP',
    'max_cahsout_exceeded' => 'Maximum cashout amount exceeded, The amount you can withdraw is :cashout_amount EGP',

    // ============================== Aeva Cab ==============================

    'schedule_request_failed' => 'Schedule request failed',
    'search_request_failed' => 'Search request failed',
    'request_inprogress' => 'Request in progress.',
    'request_drivers_failed' => 'Request drivers failed',
    'unavailable_car_type' => 'Unavailable car type',
    'no_available_drivers' => 'No available drivers.',
    'accept_request' => 'Accept Request',
    'accept_request_body' => 'Please accept the incoming request.',
    'accept_request_failed' => 'Accept request failed.',
    'request_accepted' => 'Request Accepted',
    'request_accepted_body' => 'Your request successfully accepted.',
    'update_request_status_failed' => 'Update request status failed.',
    'start_ride_failed' => 'Start ride failed.',
    'end_ride_failed' => 'End ride failed.',
    'driver_arrived' => 'Driver Arrived',
    'driver_arrived_body' => 'The driver successfully arrived at the ride location.',
    'ride_started' => 'Ride Started',
    'ride_started_body' => 'Your ride successfully started.',
    'ride_ended' => 'Ride Ended',
    'ride_ended_body' => 'Your ride successfully ended.',
    'cancel_cab_request_failed' => 'Cancel request failed.',
    'request_cancelled' => 'Request Cancelled',
    'request_cancelled_body' => 'Your request successfully cancelled.',
    'request_not_found' => 'Request not found.',
    'ride_redirection' => 'Ride Redirection',
    'ride_redirection_body' => 'Ride destination location has been changed',
    'request_already_cancelled' => 'Request already cancelled',
    'out_of_coverage_area' => 'Out of coverage area',
    'ride_completed' => 'Ride Completed',
    'ride_completed_body' => 'Ride completed successfully.',
    'request_already_accepted_by_another_driver' => 'Request already accepted by another driver',
    'update_status_failed' => 'Update status failed, a trip is in progress that you have to finish first',
    'request_already_paid' => 'Request already paid',
    'amount_can_not_be_zero' => 'Amount paid cannot be zero',
    'scanAndPayCashbackTitle' => 'congratulations .. scan and pay cashback reward',
    'scanAndPayCashbackBody' => 'you have a cashback as a gift for using our wallet',
    'your_account_is_still_suspended' => 'Your account is still suspended',
    'accept_request_failed_try_again_later' => 'Accept request failed, Try again later',
    'trx_exists' => 'There is already a payment transaction for this trip',
    'too_far_from_pickup_point' => 'Too far from the pickup point!',
    'update_app_version' => 'Please update the app to the latest version',
];
