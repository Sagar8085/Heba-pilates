<?php

namespace Tests\Http\Controllers\Admin\Member;

use App\Http\Requests\Member\StoreMembershipRequest;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use App\Services\CheckoutSession;
use App\Services\Stripe;
use Database\Seeders\SubscriptionTierSeeder;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Tests\Http\Controllers\ControllerTest;

class MembershipControllerCheckoutTest extends ControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->member = $this->user->memberProfile;

        Carbon::setTestNow(Carbon::create(2020, 1, 1));
    }

    /** @test */
    public function it_can_create_a_checkout_session()
    {
        $memberUrl = config('app.url') . '/admin/members/' . $this->user->id;

        $tier = SubscriptionTier::factory()->create([
            'stripe_price_id' => 'price-id',
        ]);

        $this->mock(Stripe::class, function ($mock) {
            $mock
                ->shouldReceive('findOrCreateCustomer')
                ->once()
                ->andReturn(new Customer('customer-id'));
        });

        $this->mock(CheckoutSession::class, function ($mock) use ($memberUrl, $tier) {
            $mock
                ->shouldReceive('setAllowsPromotionCodes')
                ->once()
                ->andReturn($mock);

            $mock
                ->shouldReceive('setMode')
                ->with(CheckoutSession::MODE_SUBSCRIPTION)
                ->once()
                ->andReturn($mock);

            $mock
                ->shouldReceive('setCustomer')
                ->with('customer-id')
                ->once()
                ->andReturn($mock);

            $mock
                ->shouldReceive('create')
                ->with([
                    'line_items' => [[
                        'price' => $tier->stripe_price_id,
                        'quantity' => 1,
                    ]],
                    'success_url' => $memberUrl . '/membership/callback/success?session_id={CHECKOUT_SESSION_ID}&tier=' . $tier->slug,
                    'cancel_url' => $memberUrl . '?membership=purchase_cancelled',
                ])
                ->once()
                ->andReturn(new Session('session-id'));
        });

        $this
            ->store(
                'member.membership.checkout',
                [
                    'tier' => $tier->slug,
                    'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_BACS,
                ],
                [
                    'member' => $this->user->id,
                ]
            )
            ->assertOk()
            ->assertJson([
                'id' => 'session-id',
            ]);
    }

    /**
     * @test
     */
    public function it_requires_a_valid_tier_to_create_a_checkout()
    {
        $this->store('member.membership.checkout', ['tier' => 'invalid-tier'], ['member' => $this->user->id])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tier');
    }

    /**
     * @test
     */
    public function it_requires_a_valid_type_to_create_a_checkout()
    {
        $this->store('member.membership.checkout', ['type' => 'invalid-type'], ['member' => $this->user->id])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('type');
    }

    /** @test */
    public function it_can_process_a_checkout_callback()
    {
        $tier = SubscriptionTier::factory()->create([
            'stripe_price_id' => 'price-id',
        ]);

        $this->mock(Stripe::class, function ($mock) {
            $intent = new PaymentIntent('payment-intent-id');
            $intent->payment_method = 'payment-method-id';

            $mock
                ->shouldReceive('paymentIntentForSession')
                ->once()
                ->andReturn($intent);
        });

        $this
            ->store(
                'member.membership.callback',
                [
                    'tier' => $tier->slug,
                    'stripeSessionID' => 'session-id',
                ],
                ['member' => $this->user->id]
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
            'renew' => 1,
            'online_credits' => $tier->online_credits,
            'studio_credits' => $tier->studio_credits,
            'stripe_id' => 'payment-method-id',
            'stripe_payment_intent' => 'payment-intent-id',
        ]);

        $this->assertDatabaseHas('events', [
            'message' => 'Purchased Membership via Admin Panel',
            'user_id' => $this->user->id,
            'object_id' => Subscription::first()->id,
            'object_type' => Subscription::class,
        ]);
    }

    /**
     * @test
     */
    public function it_requires_a_valid_tier_on_callback()
    {
        $this->store('member.membership.callback', ['tier' => 'invalid-tier'], ['member' => $this->user->id])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tier');
    }

    /**
     * @test
     */
    public function it_requires_a_session_id_on_callback()
    {
        $this->store('member.membership.callback', [], ['member' => $this->user->id])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('stripeSessionID');
    }
}
