<?php

namespace Tests\Http\Controllers\Admin\Member;

use App\Http\Requests\Member\CreditPackRequest;
use App\Http\Requests\Member\StoreCreditPackRequest;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Order;
use App\Services\CheckoutSession;
use App\Services\Stripe;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Tests\Http\Controllers\ControllerTest;

class CreditPackControllerCheckoutTest extends ControllerTest
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

    public function it_can_create_a_checkout_session()
    {
        $memberUrl = config('app.url') . '/admin/members/' . $this->user->id;

        $pack = CreditPack::factory()->create([
            'days_till_expiry' => null,
            'months_till_expiry' => 1,
            'stripe_price_id' => 'price-id',
        ]);

        $this->mock(Stripe::class, function ($mock) {
            $mock
                ->shouldReceive('findOrCreateCustomer')
                ->once()
                ->andReturn(new Customer('customer-id'));
        });

        $this->mock(CheckoutSession::class, function ($mock) use ($memberUrl, $pack) {
            $mock
                ->shouldReceive('setAllowsPromotionCodes')
                ->once()
                ->andReturn($mock);

            $mock
                ->shouldReceive('setMode')
                ->with(CheckoutSession::MODE_PAYMENT)
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
                    'payment_method_types' => [
                        CheckoutSession::PAYMENT_METHOD_CARD,
                    ],
                    'line_items' => [[
                        'price' => $pack->stripe_price_id,
                        'quantity' => 1,
                    ]],
                    'success_url' => $memberUrl . '/credit-packs/callback/success?session_id={CHECKOUT_SESSION_ID}&creditPackId=' . $pack->id,
                    'cancel_url' => $memberUrl . '?credit-packs=purchase_cancelled',
                ])
                ->once()
                ->andReturn(new Session('session-id'));
        });

        $this
            ->store(
                'member.pack.checkout',
                [
                    'creditPackId' => $pack->id,
                    'type' => CreditPackRequest::TYPE_CARD,
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

    /** @test */
    public function it_requires_a_valid_credit_pack_id_to_add_a_pack()
    {
        $this->store('member.pack.checkout', [], ['member' => $this->user->id])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('creditPackId');
    }

    /**
     * @test
     * @dataProvider validTypes
     */
    public function it_requires_a_valid_type_to_add_a_pack($type)
    {
        $this->store('member.pack.checkout', ['type' => $type], ['member' => $this->user->id])
            ->assertJsonMissingValidationErrors('type');
    }

    /** @test */
    public function it_can_process_a_checkout_callback()
    {
        $pack = CreditPack::factory()->create([
            'days_till_expiry' => null,
            'months_till_expiry' => 1,
            'stripe_price_id' => 'price-id',
        ]);

        $this->mock(Stripe::class, function ($mock) {
            $intent = new PaymentIntent('payment-intent-id');
            $intent->payment_method = 'payment-method-id';

            $mock
                ->shouldReceive('loadPaymentMethod')
                ->once();

            $mock
                ->shouldReceive('paymentIntentForSession')
                ->once()
                ->andReturn($intent);
        });

        $this
            ->store(
                'member.pack.callback',
                [
                    'creditPackId' => $pack->id,
                    'stripeSessionID' => 'session-id',
                ],
                ['member' => $this->user->id]
            )
            ->assertOk()
            ->assertJson([
                'status' => __('response.success'),
                'message' => __('pack.member.created'),
            ]);

        $this->assertDatabaseHas('credit_packs_purchases', [
            'user_id' => $this->user->id,
            'credit_pack_id' => $pack->id,
            'online_credits' => $pack->online_credits,
            'studio_credits' => $pack->studio_credits,
            'expires' => '2020-02-01 00:00:00',
        ]);

        $purchase = CreditPackPurchase::latest()->first();

        $this->assertDatabaseHas('events', [
            'message' => 'Purchased Credit Pack via Admin Panel',
            'user_id' => $this->user->id,
            'object_id' => $purchase->id,
            'object_type' => CreditPackPurchase::class,
        ]);

        $this->assertDatabaseHas('orders', [
            'member_id' => auth()->id(),
            'value' => $pack->price,
            'method' => 'stripe',
            'orderable_id' => $purchase->id,
            'orderable_type' => CreditPackPurchase::class,
            'stripe_order_id' => 'payment-intent-id',
        ]);

        $this->assertDatabaseHas('events', [
            'message' => 'Purchased Credit Pack via Admin Panel',
            'user_id' => $this->user->id,
            'object_id' => Order::first()->id,
            'object_type' => Order::class,
        ]);
    }

    /** @test */
    public function it_updates_the_users_payment_method_on_callback()
    {
        $pack = CreditPack::factory()->create([
            'days_till_expiry' => null,
            'months_till_expiry' => 1,
            'stripe_price_id' => 'price-id',
        ]);

        $this->mock(Stripe::class, function ($mock) {
            $intent = new PaymentIntent('payment-intent-id');
            $intent->payment_method = 'payment-method-id';

            $method = new PaymentMethod('payment-method-id');
            $method->type = 'payment-method-type';

            $mock
                ->shouldReceive('paymentIntentForSession')
                ->once()
                ->andReturn($intent);

            $mock
                ->shouldReceive('loadPaymentMethod')
                ->once()
                ->andReturn($method);
        });

        $this->store(
            'member.pack.callback',
            [
                'creditPackId' => $pack->id,
                'stripeSessionID' => 'session-id',
            ],
            ['member' => $this->user->id]
        );

        $this->assertDatabaseHas('stripe_payment_methods', [
            'user_id' => $this->user->id,
            'payment_method' => 'payment-method-id',
            'type' => 'payment-method-type',
            'default' => 1,
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

    public function validTypes()
    {
        return [
            StoreCreditPackRequest::TYPE_FREE => ['type' => StoreCreditPackRequest::TYPE_FREE],
            StoreCreditPackRequest::TYPE_PREPAID => ['type' => StoreCreditPackRequest::TYPE_PREPAID],
            StoreCreditPackRequest::TYPE_CARD => ['type' => StoreCreditPackRequest::TYPE_CARD],
        ];
    }
}
