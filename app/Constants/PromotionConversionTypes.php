<?php

namespace App\Constants;

/**
 * Class PromotionConversionTypes
 *
 * @package App\Constants;
 */
class PromotionConversionTypes
{
    public const ALL = [
        self::NO_CONVERSION,
        self::CONVERTED_TO_SUBSCRIPTION,
        self::CONVERTED_TO_CREDIT_PACK,
    ];
    const NO_CONVERSION = 'No Conversion';
    const CONVERTED_TO_SUBSCRIPTION = 'Converted to subscription';
    const CONVERTED_TO_CREDIT_PACK = 'Converted to credit pack';
}