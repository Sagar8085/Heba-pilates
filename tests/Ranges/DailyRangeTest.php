<?php

namespace Tests\Ranges;

use App\Ranges\Daily;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class DailyRangeTest
 *
 * @package Tests\Ranges
 */
class DailyRangeTest extends TestCase
{
    /** @test */
    public function it_builds_the_range_for_one_day_returning_carbon_instances(): void
    {
        $days = (new Daily)
            ->setFrom(Carbon::create(2022, 03, 16))
            ->setTo(Carbon::create(2022, 03, 16))
            ->setFormat(null)
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                Carbon::create(2022, 03, 16),
            ],
            $days->toArray()
        );
    }

    /** @test */
    public function it_builds_the_range_for_seven_days_returning_carbon_instances(): void
    {
        $days = (new Daily)
            ->setFrom(Carbon::create(2022, 03, 16))
            ->setTo(Carbon::create(2022, 03, 22))
            ->setFormat(null)
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                Carbon::create(2022, 03, 16),
                Carbon::create(2022, 03, 17),
                Carbon::create(2022, 03, 18),
                Carbon::create(2022, 03, 19),
                Carbon::create(2022, 03, 20),
                Carbon::create(2022, 03, 21),
                Carbon::create(2022, 03, 22),
            ],
            $days->toArray()
        );
    }

    /** @test */
    public function it_builds_the_range_for_seven_days_with_a_format(): void
    {
        $days = (new Daily)
            ->setFrom(Carbon::create(2022, 03, 16))
            ->setTo(Carbon::create(2022, 03, 22))
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                '16/03',
                '17/03',
                '18/03',
                '19/03',
                '20/03',
                '21/03',
                '22/03',
            ],
            $days->toArray()
        );
    }
}
