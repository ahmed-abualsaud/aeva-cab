<?php

namespace App\Enums;

class DriverTransactionTypesEnum
{
    const WALLET_DEPOSIT =  'Wallet Deposit';
    const WALLET_WITHDRAW = 'Wallet Withdraw';
    const CASHOUT = 'Cashout';
    const SCAN_AND_PAY = 'Scan And Pay';
    const SCAN_AND_PAY_CASHBACK = 'Scan And Pay Cashback';

    const ALL = [
        self::WALLET_DEPOSIT,
        self::WALLET_WITHDRAW,
        self::CASHOUT,
        self::SCAN_AND_PAY,
        self::SCAN_AND_PAY_CASHBACK,
    ];
}
