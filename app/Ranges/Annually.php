<?php

namespace App\Ranges;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;

/**
 * Class Annually
 *
 * @package App\Ranges;
 */
class Annually extends Range
{
    protected ?string $format = 'Y';

    public function getStartDate(): Carbon
    {
        return $this->getFrom()->copy()->startOfYear();
    }

    public function getEndDate(): Carbon
    {
        return $this->getTo()->copy()->startOfYear();
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
                    $start->addYear();
                }
            }
            return $dates;
        };
    }
}