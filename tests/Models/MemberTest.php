<?php

namespace Tests\Models;

use App\Models\User;
use Tests\TestCase;

/**
 * Class MemberTest
 *
 * @package Tests\Models;
 */
class MemberTest extends TestCase
{
    /** @test */
    public function it_belongs_to_a_user(): void
    {
        $this->assertInstanceOf(User::class, $this->createMember()->user);
    }
}