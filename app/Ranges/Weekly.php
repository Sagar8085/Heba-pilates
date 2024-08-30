<?php

namespace App\Ranges;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;

/**
 * Class Weekly
 *
 * @package App\Ranges;
 */
class Weekly extends Range
{
    public function getStartDate(): Carbon
    {
        return $this->getFrom()->copy()->startOfWeek();
    }

    public function getEndDate(): Carbon
    {
        return $this->getTo()->copy()->startOfWeek();
    }

    protected function handleEqualStartAndEnd(Carbon $start, Carbon $end): Closure
    {
        return fn (Collection $dates) => $start->isSameDay($end)
            ? $dates->push($this->getFormattedDate($start->copy()))
            : $dates;
    }

    protected function handleIncrement(Carbon $start, Carbon $end): Closure
    {
        return function (Collection $dates) use ($start, $end) {
            if ($dates->isEmpty()) {
                while ($start->lte($end)) {
                    $dates->push($this->getFormattedDate($start->copy()));
                    $start->addWeek();
                }
            }
            return $dates;
        };
    }
}
