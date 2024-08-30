<?php

namespace App\Ranges;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;

/**
 * Class Monthly
 *
 * @package App\Ranges;
 */
class Monthly extends Range
{
    public function getStartDate(): Carbon
    {
        return $this->getFrom()->copy()->startOfMonth();
    }

    public function getEndDate(): Carbon
    {
        return $this->getTo()->copy()->startOfMonth();
    }

    protected function handleEqualStartAndEnd(Carbon $start, Carbon $end): Closure
    {
        return fn (Collection $dates) => $start->isSameDay($end)
            ? $dates->push($this->getFormattedDate($start->copy()))
            : $dates;
    }

    protected function handleIncrement(Carbon $start, Carbon $end)
    {
        return function (Collection $dates) use ($start, $end) {
            if ($dates->isEmpty()) {
                while ($start->lte($end)) {
                    $dates->push($this->getFormattedDate($start->copy()));
                    $start->addMonth();
                }
            }
            return $dates;
        };
    }
}
