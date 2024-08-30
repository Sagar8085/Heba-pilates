<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class LeadCollection
 *
 * @package App\Collections
 */
class LeadCollection extends Collection
{
    /**
     * @return LeadCollection
     */
    public function withoutConversions(): LeadCollection
    {
        return $this->filter->withoutConversion();
    }

    /**
     * @return LeadCollection
     */
    public function convertedToSubscription(): LeadCollection
    {
        return $this->filter->convertedToSubscription();
    }

    /**
     * @return LeadCollection
     */
    public function convertedToCreditPack(): LeadCollection
    {
        return $this->filter->convertedToCreditPack();
    }
}
