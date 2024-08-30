<?php

namespace App\Factories;

use App\Constants\Unit;
use App\Contracts\Factory;
use App\Exceptions\InvalidTimeUnit;
use App\Ranges\Annually;
use App\Ranges\Daily;
use App\Ranges\Monthly;
use App\Ranges\Range;
use App\Ranges\Weekly;
use Illuminate\Support\Arr;
use Throwable;

/**
 * Class RangeFactory
 *
 * @package App\Factories;
 */
class RangeFactory implements Factory
{
    private array $ranges = [
        Unit::DAY => Daily::class,
        Unit::WEEK => Weekly::class,
        Unit::MONTH => Monthly::class,
        Unit::YEAR => Annually::class,
    ];

    /**
     * @throws Throwable
     */
    public function get(string $value): Range
    {
        throw_unless(in_array($value, array_keys($ranges = $this->getRanges())), InvalidTimeUnit::class);

        $class = Arr::get($ranges, $value);

        return new $class;
    }

    public function getRanges(): array
    {
        return $this->ranges;
    }
}