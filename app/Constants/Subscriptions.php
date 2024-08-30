<?php

namespace App\Constants;

/**
 * Class Subscriptions
 *
 * @package App\Constants;
 */
class Subscriptions
{
    public const ALL = [
        self::UNLIMITED_ANNUAL_VIPS,
        self::UNLIMITED_MONTHLY,
        self::PREMIUM_MONTHLY,
        self::STANDARD_MONTHLY,
    ];
    const UNLIMITED_ANNUAL_VIPS = 'Unlimited Annual VIPs';
    const UNLIMITED_MONTHLY = 'Unlimited Monthly';
    const PREMIUM_MONTHLY = 'Premium Monthly';
    const STANDARD_MONTHLY = 'Standard Monthly';
}