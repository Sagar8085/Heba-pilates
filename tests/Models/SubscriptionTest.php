<?php

namespace Tests\Models;

use App\Models\Subscription;
use App\Models\User;
use Tests\TestCase;

/**
 * Class SubscriptionTest
 *
 * @package Tests\Models
 */
class SubscriptionTest extends TestCase
{
    /** @test */
    public function it_belongs_to_a_user(): void
    {
        $this->assertInstanceOf(User::class, Subscription::factory()->create()->user);
    }
}
