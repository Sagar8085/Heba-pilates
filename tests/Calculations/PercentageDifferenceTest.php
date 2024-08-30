<?php

namespace Tests\Calculations;

use App\Calculations\PercentageDifference;
use PHPUnit\Framework\TestCase;

/**
 * Class PercentageDifferenceTest
 *
 * @package Tests
 */
class PercentageDifferenceTest extends TestCase
{
    /**
     * @test
     * @dataProvider calculations
     */
    public function it_calculates_the_percentage_difference_between_two_numbers($first, $second, $result): void
    {
        $this->assertEquals(
            $result,
            (new PercentageDifference($first, $second))->calculate()
        );
    }

    /**
     * @return array
     */
    public function calculations(): array
    {
        return [
            [
                1,
                2,
                '+100%',
            ],
            [
                4,
                7,
                '+75%',
            ],
            [
                2,
                3,
                '+50%',
            ],
            [
                4,
                5,
                '+25%',
            ],
            [
                1,
                1,
                '0%',
            ],
            [
                100,
                0,
                '-100%',
            ],
            [
                100,
                25,
                '-75%',
            ],
            [
                100,
                50,
                '-50%',
            ],
            [
                100,
                75,
                '-25%',
            ],
        ];
    }
}
