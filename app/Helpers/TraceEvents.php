<?php

namespace App\Helpers;

class TraceEvents
{
    const LOGIN = 'login';
    const RECEIVED_CAB_REQUEST = 'received cab request';
    const ACCEPT_CAB_REQUEST = 'accept cab request';
    const ARRIVED_CAB_REQUEST = 'arrived cab request';
    const START_CAB_REQUEST = 'start cab request';
    const END_CAB_REQUEST = 'end cab request';
    const CANCEL_CAB_REQUEST = 'cancel cap request';
    const COMPLETE_CAB_REQUEST = 'complete cab request';
    const MISSED_CAB_REQUEST = 'missed cab request';
    const CASHOUT = 'cashout';
    const GO_OFFLINE = 'go offline';
    const GO_ONLINE = 'go online';
    const LOG_OUT = 'log out';

    const DRIVER = [
        self::LOGIN,
        self::RECEIVED_CAB_REQUEST,
        self::ACCEPT_CAB_REQUEST,
        self::ARRIVED_CAB_REQUEST,
        self::START_CAB_REQUEST,
        self::END_CAB_REQUEST,
        self::CANCEL_CAB_REQUEST,
        self::COMPLETE_CAB_REQUEST,
        self::MISSED_CAB_REQUEST,
        self::CASHOUT,
        self::GO_OFFLINE,
        self::GO_ONLINE,
        self::LOG_OUT,
    ];
}
