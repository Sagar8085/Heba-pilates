<?php

namespace Tests\Traits;

/**
 * Trait HandlesSubscriptionStatuses
 *
 * @package Tests\Traits
 */
trait HandlesSubscriptionStatuses
{
    public function subscriptionStatuses(): array
    {
        return [
            ['expired', '{"name":"Expired","value":"expired"}'],
            ['deleted', '{"name":"Deleted","value":"Deleted"}'],
            ['active-does-not-renew', '{"name":"Active - Does Not Renew","value":"active-does-not-renew"}'],
            ['active-renews', '{"name":"Active - Renews","value":"active-renews"}'],
        ];
    }
}