<?php

namespace Tests\Factories;

use App\Exceptions\InvalidTimeUnit;
use App\Factories\RangeFactory;
use App\Ranges\Annually;
use App\Ranges\Daily;
use App\Ranges\Monthly;
use App\Ranges\Weekly;
use Tests\TestCase;
use Throwable;

/**
 * Class RangeFactoryTest
 *
 * @package Tests\Factories
 */
class RangeFactoryTest extends TestCase
{
    /** @test
     * @throws Throwable
     */
    public function it_throws_an_exception_if_the_time_unit_is_invalid(): void
    {
        $this->expectException(InvalidTimeUnit::class);
        (new RangeFactory)->get('invalid type');
    }

    /**
     * @test
     * @dataProvider ranges
     * @throws Throwable
     */
    public function it_returns_a_range_class_for_the_respective_key($unit, $class): void
    {
        $this->assertInstanceOf($class, (new RangeFactory)->get($unit));
    }

    public function ranges(): array
    {
        return [
            [
                'day',
                Daily::class,
            ],
            [
                'week',
                Weekly::class,
            ],
            [
                'month',
                Monthly::class,
            ],
            [
                'year',
                Annually::class,
            ],
        ];
    }
}
