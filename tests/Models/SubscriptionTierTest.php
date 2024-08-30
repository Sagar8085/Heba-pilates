<?php

namespace Tests\Models;

use App\Models\SubscriptionTier;
use App\Models\User;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SubscriptionTierTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2020, 1, 1));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider subscriptionOptions
     */
    public function it_can_generate_a_subscription_for_a_user($renews)
    {
        $user = (new User())->forceFill([
            'id' => 1,
        ]);

        $tier = (new SubscriptionTier())->forceFill([
            'slug' => 'vip-unlimited',
            'online_credits' => 10000,
            'studio_credits' => 10000,
            'months_till_expiry' => 12,
        ]);

        $subscription = $tier->memberSubscription($user, $renews);

        $this->assertEquals(1, $subscription->user_id);
        $this->assertEquals('vip-unlimited', $subscription->tier);
        $this->assertEquals(12, $subscription->expires->diffInMonths());
        $this->assertEquals(10000, $subscription->online_credits);
        $this->assertEquals(10000, $subscription->studio_credits);
        $this->assertEquals($renews, $subscription->renew);
    }

    /** @test */
    public function it_defaults_to_non_renewing_when_generating_a_subscription_for_a_user()
    {
        $user = new User();
        $tier = new SubscriptionTier();

        $subscription = $tier->memberSubscription($user);

        $this->assertFalse($subscription->renew);
    }

    public function subscriptionOptions()
    {
        return [
            'renews' => ['renews' => true],
            'does-not-renew' => ['renews' => false],
        ];
    }
}
