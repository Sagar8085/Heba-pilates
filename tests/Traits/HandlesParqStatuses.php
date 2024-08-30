<?php

namespace Tests\Traits;

/**
 * Trait HandlesParqStatuses
 *
 * @package Tests\Traits
 */
trait HandlesParqStatuses
{
    public function parqStatuses(): array
    {
        return [
            ['completed', '{"name":"Completed","value":"completed"}'],
            ['not-completed', '{"name":"Not Completed","value":"not-completed"}'],
        ];
    }
}
