<?php

namespace Tests\Http\Controllers\Dashboard;

use Tests\TestCase;

/**
 * Class SubscriptionTypeControllerTest
 *
 * @package Tests\Http\Controllers\Dashboard
 */
class SubscriptionTypeControllerTest extends TestCase
{
    /** @test */
    public function it_cannot_be_accessed_if_unauthorised(): void
    {
        $this->getJson(route('subscription-type.index'))->assertUnauthorized();
    }

    /** @test */
    public function it_gets_a_list_of_all_subscription_type(): void
    {
        $this->signIn()
            ->getJson(route('subscription-type.index'))
            ->assertJson([
                [
                    'slug' => 'premium-membership-subscription',
                    'name' => 'Premium Membership Subscription',
                ],
                [
                    'slug' => 'one-month-unlimited',
                    'name' => 'One Month Unlimited',
                ],
                [
                    'slug' => 'premium-membership',
                    'name' => 'Premium Membership',
                ],
                [
                    'slug' => 'vip-unlimited',
                    'name' => 'Vip Unlimited',
                ],
                [
                    'slug' => 'unlimited-membership',
                    'name' => 'Unlimited Membership',
                ],
                [
                    'slug' => 'unlimited-membership-subscription',
                    'name' => 'Unlimited Membership Subscription',
                ],
                [
                    'slug' => 'premium',
                    'name' => 'Premium',
                ],
                [
                    'slug' => 'standard',
                    'name' => 'Standard',
                ],
            ]);
    }
}
