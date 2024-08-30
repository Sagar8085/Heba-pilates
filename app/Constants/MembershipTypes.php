<?php

namespace App\Constants;

/**
 * Class MembershipTypes
 *
 * @package App\Constants;
 */
class MembershipTypes
{
    public const ALL = [
        self::ACTIVE,
        self::EXPIRED,
    ];
    const ACTIVE = 'active';
    const EXPIRED = 'expired';
}