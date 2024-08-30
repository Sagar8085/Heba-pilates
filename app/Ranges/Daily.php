<?php

namespace App\Ranges;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;

/**
 * Class Daily
 *
 * @package App\Ranges;
 */
class Daily extends Range
{
    public function setFrom(Carbon $from): Range
    {
        return parent::setFrom($from->startOfDay());
    }

    public function setTo(Carbon $to): Range
    {
        return parent::setTo($to->endOfDay());
    }

    public function getStartDate(): Carbon
    {
        return $this->getFrom()->copy();
    }

    public function getEndDate(): Carbon
    {
        return $this->getTo()->copy();
    }

    protected function handleEqualStartAndEnd(Carbon $start, Carbon $end): Closure
    {
        return fn (Collection $dates) => $dates;
    }

    protected function handleIncrement(Carbon $start, Carbon $end): Closure
    {
        return function (Collection $dates) use ($start, $end) {
            if ($dates->isEmpty()) {
                while ($start->lt($end)) {
                    $dates->push($this->getFormattedDate($start->copy()));
                    $start->addDay();
                }
            }
            return $dates;
        };
    }
}
