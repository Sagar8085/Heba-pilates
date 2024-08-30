<?php

namespace App\Charts\Orders\Line;

use App\Traits\CalculatesChurn;

/**
 * Class TotalChurnRevenue
 *
 * @package App\Charts\Orders\Line;
 */
class TotalChurnRevenue extends TotalOrderRevenue
{
    use CalculatesChurn;
}