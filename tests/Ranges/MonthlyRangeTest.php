<?php

namespace Tests\Ranges;

use App\Ranges\Monthly;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class MonthlyRangeTest
 *
 * @package Tests\Ranges
 */
class MonthlyRangeTest extends TestCase
{
    /** @test */
    public function it_builds_the_range_for_one_month_returning_carbon_instances(): void
    {
        $days = (new Monthly)
            ->setFrom(Carbon::create(2022, 03, 05))
            ->setTo(Carbon::create(2022, 03, 25))
            ->setFormat(null)
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                Carbon::create(2022, 03, 01),
            ],
            $days->toArray()
        );
    }

    /** @test */
    public function it_builds_the_range_for_seven_months_returning_carbon_instances(): void
    {
        $days = (new Monthly)
            ->setFrom(Carbon::create(2022, 01, 16))
            ->setTo(Carbon::create(2022, 12, 22))
            ->setFormat(null)
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                Carbon::create(2022, 1, 01),
                Carbon::create(2022, 2, 01),
                Carbon::create(2022, 3, 01),
                Carbon::create(2022, 4, 01),
                Carbon::create(2022, 5, 01),
                Carbon::create(2022, 6, 01),
                Carbon::create(2022, 7, 01),
                Carbon::create(2022, 8, 01),
                Carbon::create(2022, 9, 01),
                Carbon::create(2022, 10, 01),
                Carbon::create(2022, 11, 01),
                Carbon::create(2022, 12, 01),
            ],
            $days->toArray()
        );
    }

    /** @test */
    public function it_builds_the_range_for_seven_days_with_a_format(): void
    {
        $days = (new Monthly)
            ->setFrom(Carbon::create(2022, 01, 16))
            ->setTo(Carbon::create(2022, 12, 22))
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                '01/01',
                '01/02',
                '01/03',
                '01/04',
                '01/05',
                '01/06',
                '01/07',
                '01/08',
                '01/09',
                '01/10',
                '01/11',
                '01/12',
            ],
            $days->toArray()
        );
    }
}
