<?php

namespace Tests\Rules;

use App\Rules\ValidComparisonRange;
use Tests\TestCase;

/**
 * Class ValidComparisonRangeTest
 *
 * @package Tests\Rules
 */
class ValidComparisonRangeTest extends TestCase
{
    /** @test */
    public function it_passes_if_the_date_ranges_are_the_same(): void
    {
        $this->assertTrue(
            (new ValidComparisonRange([
                'report_date_from' => '2022-01-01',
                'report_date_to' => '2022-01-10',
                'compare_date_to' => '2022-02-10',
            ]))->passes('compare_date_from', '2022-02-01')
        );
    }

    /** @test */
    public function it_fails_if_the_date_ranges_are_different(): void
    {
        $this->assertFalse(
            (new ValidComparisonRange([
                'report_date_from' => '2022-01-01',
                'report_date_to' => '2022-01-10',
                'compare_date_to' => '2022-02-10',
            ]))->passes('compare_date_from', '2022-02-02')
        );
    }

    /** @test */
    public function it_passes_if_null_values_are_provided(): void
    {
        $this->assertTrue(
            (new ValidComparisonRange([
                'report_date_from' => null,
                'report_date_to' => null,
                'compare_date_to' => null,
            ]))->passes('compare_date_from', null)
        );
    }

    /** @test */
    public function it_has_the_correct_error_message(): void
    {
        $this->assertEquals(
            'The date ranges are invalid',
            (new ValidComparisonRange([]))->message()
        );
    }
}
