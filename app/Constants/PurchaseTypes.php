<?php

namespace App\Constants;

/**
 * Class PurchaseTypes
 *
 * @package App\Constants;
 */
class PurchaseTypes
{
    public const ALL = [
        self::PROMO_PURCHASES,
        self::SUBSCRIPTION_PURCHASES,
        self::NEW_SUBSCRIPTION_PURCHASES,
        self::CREDIT_PACK_PURCHASES,
        self::FREE_CREDIT_PLANS,
        self::VIP_CREDIT_PLANS,
        self::CORPORATE_CREDIT_PLANS,
    ];
    const PROMO_PURCHASES = 'Promo Purchases';
    const SUBSCRIPTION_PURCHASES = 'Subscriptions Purchases';
    const NEW_SUBSCRIPTION_PURCHASES = 'New Subscriptions Purchases';
    const CREDIT_PACK_PURCHASES = 'Credit Pack Purchases';
    const FREE_CREDIT_PLANS = 'Free Credits/Packs/Plans';
    const VIP_CREDIT_PLANS = 'VIP/Partner Credit Plans';
    const CORPORATE_CREDIT_PLANS = 'Corporate Credit Plans';
}