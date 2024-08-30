<?php

namespace Tests\Http\Controllers\Admin\Member;

use App\Http\Requests\Member\StoreMembershipRequest;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tests\Http\Controllers\ControllerTest;

class MembershipControllerStoreTest extends ControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->member = $this->user->memberProfile;

        Carbon::setTestNow(Carbon::create(2020, 1, 1));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    /** @test */
    public function it_stores_a_free_subscription_for_a_member()
    {
        $tier = SubscriptionTier::factory()->create();

        $this
            ->store(
                'member.membership.store',
                [
                    'tier' => $tier->slug,
                    'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_FREE,
                ],
                [
                    'member' => $this->user->id,
                ]
            )
            ->assertOk()
            ->assertJson([
                'status' => __('response.success'),
                'message' => __('membership.member.created'),
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'tier' => $tier->slug,
            'expires' => '2020-02-01 00:00:00',
            'renew' => 0,
            'online_credits' => $tier->online_credits,
            'studio_credits' => $tier->studio_credits,
        ]);

        $this->assertDatabaseHas('events', [
            'message' => 'Free Membership Added via Admin Panel',
            'user_id' => $this->user->id,
            'object_id' => Subscription::first()->id,
            'object_type' => Subscription::class,
        ]);
    }

    /** @test */
    public function it_stores_an_already_paid_subscription_for_a_member()
    {
        $tier = SubscriptionTier::factory()->create();

        $this
            ->store(
                'member.membership.store',
                [
                    'tier' => $tier->slug,
                    'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_PAID,
                ],
                [
                    'member' => $this->user->id,
                ]
            )
            ->assertOk()
            ->assertJson([
                'status' => __('response.success'),
                'message' => __('membership.member.created'),
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'tier' => $tier->slug,
            'expires' => '2020-02-01 00:00:00',
            'renew' => 0,
            'online_credits' => $tier->online_credits,
            'studio_credits' => $tier->studio_credits,
        ]);

        $subscription = Subscription::first();

        $this->assertDatabaseHas('events', [
            'message' => 'Already-Paid Membership added via Admin Panel',
            'user_id' => $this->user->id,
            'object_id' => $subscription->id,
            'object_type' => Subscription::class,
        ]);

        $this->assertDatabaseHas('orders', [
            'member_id' => $this->user->id,
            'value' => $tier->price,
            'method' => Order::METHOD_STRIPE,
            'orderable_id' => $subscription->id,
            'orderable_type' => Subscription::class,
            'stripe_order_id' => 0
        ]);
    }

    /**
     * @test
     * @dataProvider invalidTierDataProvider
     */
    public function it_requires_a_valid_tier_to_create_a_subscription($tier)
    {
        $this->store('member.membership.store', ['tier' => $tier], ['member' => $this->user->id])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tier');
    }

    /**
     * @test
     * @dataProvider typeDataProvider
     */
    public function it_requires_a_valid_type_to_create_a_subscription($type)
    {
        $this->store('member.membership.store', ['type' => $type], ['member' => $this->user->id])
            ->assertJsonMissingValidationErrors('type');
    }

    public function invalidTierDataProvider()
    {
        return [
            'null_tier' => ['tier' => null],
            'invalid_tier' => ['tier' => 'invalid_tier'],
        ];
    }

    public function typeDataProvider()
    {
        return [
            'free' => ['type' => 'free'],
            'already-paid' => ['type' => 'already-paid'],
            'bacs' => ['type' => 'bacs'],
        ];
    }
}
