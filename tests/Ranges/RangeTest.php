<?php

namespace Tests\Ranges;

use App\Ranges\Range;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Class RangeTest
 */
class RangeTest extends TestCase
{
    /**
     * @test
     * @dataProvider dates
     */
    public function it_formats_the_date_correctly($date, $format, $result): void
    {
        $this->assertEquals(
            $result,
            (new TestRange)->setFormat($format)->getFormattedDate($date)
        );
    }

    public function dates(): array
    {
        return [
            [
                Carbon::create(2022, 01, 01),
                'd/m',
                '01/01',
            ],
            [
                Carbon::create(2022, 01, 01),
                null,
                Carbon::create(2022, 01, 01),
            ],
        ];
    }
}

class TestRange extends Range
{
    public function build(): Range
    {
        // Not needed for current tests
    }

    public function getStartDate(): Carbon
    {
        // Not needed for current tests
    }

    public function getEndDate(): Carbon
    {
        // Not needed for current tests
    }

    protected function handleEqualStartAndEnd(Carbon $start, Carbon $end)
    {
        // Not need for current tests
    }

    protected function handleIncrement(Carbon $start, Carbon $end)
    {
        // Not need for current tests
    }
}
