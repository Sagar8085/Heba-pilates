<?php

namespace Tests\Collections;

use Tests\TestCase;

/**
 * Class CollectionTest
 *
 * @package Tests\Collections
 */
class CollectionTest extends TestCase
{
    /** @test */
    public function it_extracts_any_string(): void
    {
        $this->assertEquals(
            [
                'Not Set',
            ],
            collect([
                '{"slug":"null","name":"Not Set"}',
            ])
                ->extract('name')
                ->toArray()
        );
    }

    /** @test */
    public function it_converts_a_string_to_an_array_of_integers(): void
    {
        $this->assertEquals(
            range(0, 25),
            collect([
                '{"slug":"0-25","name":"Up to 25"}',
            ])
                ->extract('slug')
                ->removeTextualValues()
                ->flatMap(fn (string $range) => range(...explode('-', $range)))
                ->toArray()
        );
    }

    /** @test */
    public function it_converts_multiple_strings_to_an_array_of_integers(): void
    {
        $this->assertEquals(
            [
                ...range(0, 25),
                ...range(41, 55),
            ],
            collect([
                '{"slug": "0-25", "name":"Up to 25"}',
                '{"slug": "41-55", "name": "41-55"}',
            ])
                ->extract('slug')
                ->removeTextualValues()
                ->flatMap(fn (string $range) => range(...explode('-', $range)))
                ->toArray()
        );
    }
}
