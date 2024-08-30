<?php

namespace Tests\Ranges;

use App\Ranges\Annually;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class AnnuallyRangeTest
 *
 * @package Tests\Ranges
 */
class AnnuallyRangeTest extends TestCase
{
    /** @test */
    public function it_builds_the_range_for_one_year_returning_carbon_instances(): void
    {
        $days = (new Annually)
            ->setFrom(Carbon::create(2022, 03, 05))
            ->setTo(Carbon::create(2022, 03, 25))
            ->setFormat(null)
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                Carbon::create(2022, 01, 01),
            ],
            $days->toArray()
        );
    }

    /** @test */
    public function it_builds_the_range_for_seven_days_returning_carbon_instances(): void
    {
        $days = (new Annually)
            ->setFrom(Carbon::create(2022, 01, 16))
            ->setTo(Carbon::create(2032, 12, 22))
            ->setFormat(null)
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                Carbon::create(2022, 1, 01),
                Carbon::create(2023, 1, 01),
                Carbon::create(2024, 1, 01),
                Carbon::create(2025, 1, 01),
                Carbon::create(2026, 1, 01),
                Carbon::create(2027, 1, 01),
                Carbon::create(2028, 1, 01),
                Carbon::create(2029, 1, 01),
                Carbon::create(2030, 1, 01),
                Carbon::create(2031, 1, 01),
                Carbon::create(2032, 1, 01),
            ],
            $days->toArray()
        );
    }

    /** @test */
    public function it_builds_the_range_for_seven_days_with_a_format(): void
    {
        $days = (new Annually)
            ->setFrom(Carbon::create(2022, 01, 16))
            ->setTo(Carbon::create(2032, 12, 22))
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                '2022',
                '2023',
                '2024',
                '2025',
                '2026',
                '2027',
                '2028',
                '2029',
                '2030',
                '2031',
                '2032',
            ],
            $days->toArray()
        );
    }
}
