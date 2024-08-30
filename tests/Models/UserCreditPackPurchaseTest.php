<?php

namespace Tests\Models;

use App\Models\CreditPack;
use Tests\TestCase;

/**
 * Class UserCreditPackPurchaseTest
 *
 * @package Tests\Models;
 */
class UserCreditPackPurchaseTest extends TestCase
{
    /** @test */
    public function it_has_many_credit_pack_purchases(): void
    {
        $user = $this->createUser();
        $this->createCreditPackPurchase(5, [
            'user_id' => $user->getKey(),
            'order_id' => $this->createOrder(null, [
                'orderable_id' => $this->createCreditPack()->getKey(),
                'orderable_type' => CreditPack::class,
            ])->getKey(),
        ]);

        $this->assertCount(5, $user->creditPackPurchases);
    }
    
    /** @test */
    public function it_has_a_most_recent_credit_pack(): void
    {
        $user = $this->createUser();
        $first = $this->createCreditPackPurchase(null, [
            'user_id' => $user->getKey(),
            'order_id' => $this->createOrder(null, [
                'orderable_id' => $this->createCreditPack()->getKey(),
                'orderable_type' => CreditPack::class,
            ])->getKey(),
            'expires' => now()->subWeek(),
            'created_at' => now()->subWeek(),
        ]);
        $second = $this->createCreditPackPurchase(null, [
            'user_id' => $user->getKey(),
            'order_id' => $this->createOrder(null, [
                'orderable_id' => $this->createCreditPack()->getKey(),
                'orderable_type' => CreditPack::class,
            ])->getKey(),
            'expires' => now()->addWeek(),
            'created_at' => now()->addWeek(),
        ]);

        $this->assertTrue($second->is($user->creditPackPurchase));
    }
}