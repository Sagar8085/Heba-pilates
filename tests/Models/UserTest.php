<?php

namespace Tests\Models;

use App\Models\Gym;
use Tests\TestCase;

/**
 * Class UserTest
 *
 * @package Tests\Models;
 */
class UserTest extends TestCase
{
    /** @test */
    public function it_has_a_gym(): void
    {
        $this->assertInstanceOf(Gym::class, $this->createMember()->user->gym);
    }
}