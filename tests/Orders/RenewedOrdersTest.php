<?php

namespace Tests\Orders;

use App\Models\CreditPack;
use App\Models\Order;
use Tests\TestCase;

/**
 * Class RenewedOrdersTest
 *
 * @package Tests\Orders;
 */
class RenewedOrdersTest extends TestCase
{
    /** @test */
    public function it_has_related_orders_of_the_same_time(): void
    {
        [$first] = $this->createOrderForCreditPack([
            'member_id' => $this->createAdministrator()->memberProfile->id,
        ], 10);

        $this->assertTrue($first->relatedOrders->every(fn (Order $order
        ) => $order->orderable_type === CreditPack::class));
    }

    /** @test */
    public function it_knows_if_it_is_a_renewal_of_a_previous_credit_pack_order(): void
    {
        $member = $this->createAdministrator()->memberProfile;

        $creditPack = $this->createCreditPack(null, [
            'promotional' => false,
        ]);

        $this->createOrderForCreditPack(
            [
                'expires' => now()->subDays(6),
                'member_id' => $member->id,
            ],
            1,
            $creditPack
        );

        /** @var Order $second */
        $second = $this->createOrderForCreditPack(
            [
                'expires' => now()->addWeek(),
                'member_id' => $member->id,
            ],
            1,
            $creditPack
        )->first();

        $this->assertTrue($second->isARenewal());
    }

    /** @test */
    public function it_knows_if_it_is_not_a_renewal_of_a_previous_credit_pack_order(): void
    {
        $member = $this->createAdministrator()->memberProfile;

        $creditPack = $this->createCreditPack(null, [
            'promotional' => false,
        ]);

        /** @var Order $first */
        $this->createOrderForCreditPack(
            [
                'expires' => now()->subDays(6),
            ],
            1,
            $creditPack
        );

        /** @var Order $second */
        $second = $this->createOrderForCreditPack(
            [
                'expires' => now()->addWeek(),
                'member_id' => $member->id,
            ],
            1,
            $creditPack
        )->first();

        $this->assertFalse($second->isARenewal());
    }

    /** @test */
    public function it_knows_if_it_is_not_a_renewal_of_a_previous_credit_pack_order_if_using_a_promotional_credit_pack(
    ): void
    {
        $member = $this->createAdministrator()->memberProfile;

        $creditPack = $this->createCreditPack(null, [
            'promotional' => true,
        ]);

        $this->createOrderForCreditPack(
            [
                'expires' => now()->subDays(6),
                'member_id' => $member->id,
            ],
            1,
            $creditPack
        );

        /** @var Order $order */
        $order = $this->createOrderForCreditPack(
            [
                'expires' => now()->addWeek(),
                'member_id' => $member->id,
            ],
            1,
            $creditPack
        )->first();

        $this->assertFalse($order->isARenewal());
    }
}