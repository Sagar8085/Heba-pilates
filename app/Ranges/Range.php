<?php

namespace App\Ranges;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Range
 *
 * @package App\Dates;
 */
abstract class Range
{
    private Collection $dates;

    private Carbon $from;

    private Carbon $to;

    protected ?string $format = 'd/m';

    abstract public function getStartDate(): Carbon;

    abstract public function getEndDate(): Carbon;

    abstract protected function handleEqualStartAndEnd(Carbon $start, Carbon $end);

    abstract protected function handleIncrement(Carbon $start, Carbon $end);

    public function build(): Range
    {
        $start = $this->getStartDate();
        $end = $this->getEndDate();

        return $this->setDates(
            collect()
                ->pipe($this->handleEqualStartAndEnd($start, $end))
                ->pipe($this->handleIncrement($start, $end))
        );
    }

    public function getFrom(): Carbon
    {
        return $this->from;
    }

    public function setFrom(Carbon $from): Range
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): Carbon
    {
        return $this->to;
    }

    public function setTo(Carbon $to): Range
    {
        $this->to = $to;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): Range
    {
        $this->format = $format;

        return $this;
    }

    public function getDates(): Collection
    {
        return $this->dates;
    }

    public function setDates(Collection $dates): Range
    {
        $this->dates = $dates;

        return $this;
    }

    public function getFormattedDate(Carbon $date): Carbon|string
    {
        $format = $this->getFormat();

        return $format ? $date->format($format) : $date;
    }
}