<?php

namespace App\Constants;

/**
 * Class LeadStatuses
 *
 * @package App\Constants;
 */
class LeadStatuses
{
    public const ALL = [
        self::NEW,
        self::CONTACTED,
        self::NURTURING,
        self::UNQUALIFIED,
        self::LOST,
        self::WON,
    ];

    public const NEW = 'new';
    public const CONTACTED = 'contacted';
    public const NURTURING = 'nurturing';
    public const UNQUALIFIED = 'unqualified';
    public const LOST = 'lost';
    public const WON = 'won';
}