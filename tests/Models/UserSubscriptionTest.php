<?php

namespace Tests\Models;

use Tests\TestCase;

/**
 * Class UserSubscriptionTest
 *
 * @package Tests\Models;
 */
class UserSubscriptionTest extends TestCase
{
    /** @test */
    public function it_doesnt_have_any_active_subscription(): void
    {
        $this->assertFalse($this->createUser()->hasActiveSubscription());
    }

    /** @test */
    public function it_has_an_active_default_subscription(): void
    {
        $this->assertTrue($this->createSubscription(null, [
            'expires' => now()->addWeek(),
        ])->user->hasActiveSubscription());
    }

    /** @test */
    public function it_doesnt_have_active_default_subscription(): void
    {
        $this->assertFalse($this->createSubscription(null, [
            'expires' => now()->subWeek(),
        ])->user->hasActiveSubscription());
    }

    /** @test */
    public function it_has_an_active_premium_subscription(): void
    {
        $this->assertTrue($this->createSubscription(null, [
            'expires' => now()->addWeek(),
            'tier' => 'Premium',
        ])->user->hasActiveSubscription('Premium'));
    }

    /** @test */
    public function it_doesnt_have_active_premium_subscription(): void
    {
        $this->assertFalse($this->createSubscription(null, [
            'expires' => now()->subWeek(),
            'tier' => 'Premium',
        ])->user->hasActiveSubscription('Premium'));
    }
}