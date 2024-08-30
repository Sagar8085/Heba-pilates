<?php

namespace App\Constants;

/**
 * Class PaymentMethod
 *
 * @package App\Constants;
 */
class PaymentMethods
{
    public const ALL = [
        self::APPLE,
        self::GOOGLE,
        self::STRIPE,
    ];
    const APPLE = 'apple';
    const GOOGLE = 'google';
    const STRIPE = 'stripe';
}