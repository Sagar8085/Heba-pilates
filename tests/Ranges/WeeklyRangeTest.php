<?php

namespace Tests\Ranges;

use App\Ranges\Weekly;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class WeeklyRangeTest
 *
 * @package Tests\Ranges;
 */
class WeeklyRangeTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_builds_the_range_for_one_week_returning_a_carbon_instance(): void
    {
        $days = (new Weekly)
            ->setFrom(Carbon::create(2022, 03, 16))
            ->setTo(Carbon::create(2022, 03, 18))
            ->setFormat(null)
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                Carbon::create(2022, 03, 14),
            ],
            $days->toArray()
        );
    }

    /** @test */
    public function it_builds_the_range_for_four_weeks_returning_carbon_instances(): void
    {
        $days = (new Weekly)
            ->setFrom(Carbon::create(2022, 03, 16))
            ->setTo(Carbon::create(2022, 04, 06))
            ->setFormat(null)
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                Carbon::create(2022, 03, 14),
                Carbon::create(2022, 03, 21),
                Carbon::create(2022, 03, 28),
                Carbon::create(2022, 04, 04),
            ],
            $days->toArray()
        );
    }

    /** @test */
    public function it_builds_the_range_for_four_weeks_returning_a_format(): void
    {
        $days = (new Weekly)
            ->setFrom(Carbon::create(2022, 03, 16))
            ->setTo(Carbon::create(2022, 04, 06))
            ->build()
            ->getDates();

        $this->assertEquals(
            [
                '14/03',
                '21/03',
                '28/03',
                '04/04',
            ],
            $days->toArray()
        );
    }

    /**
     * @test
     * @noinspection PhpUndefinedMethodInspection
     */
    public function it_always_builds_the_range_with_the_default_day_a_monday(): void
    {
        $start = Carbon::parse($this->faker->dateTimeBetween());
        $end = $start->copy()->addMonth();

        $this->assertTrue(
            (new Weekly)
                ->setFrom($start)
                ->setTo($end)
                ->setFormat(null)
                ->build()->getDates()->every->isMonday()
        );
    }
}