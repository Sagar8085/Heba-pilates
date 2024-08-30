<?php

return [
    \App\Constants\PurchaseTypes::PROMO_PURCHASES => [
        'colour' => '#475fc6',
    ],
    \App\Constants\PurchaseTypes::SUBSCRIPTION_PURCHASES => [
        'colour' => '#adc911',
    ],
    \App\Constants\PurchaseTypes::NEW_SUBSCRIPTION_PURCHASES => [
        'colour' => '#16b5c8',
    ],
    \App\Constants\PurchaseTypes::CREDIT_PACK_PURCHASES => [
        'colour' => '#8860ae',
    ],
    \App\Constants\PurchaseTypes::FREE_CREDIT_PLANS => [
        'colour' => '#ffad89',
    ],
    \App\Constants\PurchaseTypes::VIP_CREDIT_PLANS => [
        'colour' => '#ffda88',
    ],
    \App\Constants\PurchaseTypes::CORPORATE_CREDIT_PLANS => [
        'colour' => '#ef5998',
    ],
    \App\Constants\Subscriptions::UNLIMITED_ANNUAL_VIPS => [
        'colour' => '#ef5998',
    ],
    \App\Constants\Subscriptions::UNLIMITED_MONTHLY => [
        'colour' => '#8860ae',
    ],
    \App\Constants\Subscriptions::PREMIUM_MONTHLY => [
        'colour' => '#ffad89',
    ],
    \App\Constants\Subscriptions::STANDARD_MONTHLY => [
        'colour' => '#17B5C8',
    ],
    \App\Constants\CreditPacks::THIRTY_PACK => [
        'colour' => '#ef5998',
    ],
    \App\Constants\CreditPacks::TEN_PACK => [
        'colour' => '#8860ae',
    ],
    \App\Constants\CreditPacks::ONE_CREDIT => [
        'colour' => '#ffad89',
    ],
    \App\Constants\CreditPacks::FREE_SESSION => [
        'colour' => '#17B5C8',
    ],
    \App\Constants\PromotionConversionTypes::NO_CONVERSION => [
        'colour' => '#17B5C8',
    ],
    \App\Constants\PromotionConversionTypes::CONVERTED_TO_SUBSCRIPTION => [
        'colour' => '#ef5998',
    ],
    \App\Constants\PromotionConversionTypes::CONVERTED_TO_CREDIT_PACK => [
        'colour' => '#8860ae',
    ],
];