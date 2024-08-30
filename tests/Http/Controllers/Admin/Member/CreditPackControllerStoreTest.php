<?php

namespace Tests\Http\Controllers\Admin\Member;

use App\Http\Requests\Member\CreditPackRequest;
use App\Http\Requests\Member\StoreCreditPackRequest;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Order;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tests\Http\Controllers\ControllerTest;

class CreditPackControllerStoreTest extends ControllerTest
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
    public function it_stores_a_free_credit_pack_for_a_member()
    {
        $pack = CreditPack::factory()->create([
            'days_till_expiry' => null,
            'months_till_expiry' => 1,
        ]);

        $this
            ->store(
                'member.pack.store',
                [
                    'creditPackId' => $pack->id,
                    'type' => CreditPackRequest::TYPE_FREE,
                ],
                [
                    'member' => $this->user->id,
                ]
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

        $this->assertDatabaseHas('events', [
            'message' => 'Free Credit Pack Added',
            'user_id' => $this->user->id,
            'object_id' => CreditPackPurchase::first()->id,
            'object_type' => CreditPackPurchase::class,
        ]);
    }

    /** @test */
    public function it_stores_a_prepaid_credit_pack_for_a_member()
    {
        $pack = CreditPack::factory()->create([
            'days_till_expiry' => null,
            'months_till_expiry' => 1,
        ]);

        $this
            ->store(
                'member.pack.store',
                [
                    'creditPackId' => $pack->id,
                    'type' => CreditPackRequest::TYPE_PREPAID,
                ],
                [
                    'member' => $this->user->id,
                ]
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

        $purchase = CreditPackPurchase::first();

        $this->assertDatabaseHas('events', [
            'message' => auth()->user()->name.' added a Prepaid Credit Pack',
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
            'stripe_order_id' => '',
        ]);

        $this->assertDatabaseHas('events', [
            'message' => auth()->user()->name.' added a Prepaid Credit Pack',
            'user_id' => $this->user->id,
            'object_id' => Order::first()->id,
            'object_type' => Order::class,
        ]);
    }

    /** @test */
    public function it_requires_a_valid_credit_pack_id_to_add_a_pack()
    {
        $this->store('member.pack.store', [], ['member' => $this->user->id])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('creditPackId');
    }

    /**
     * @test
     * @dataProvider validTypes
     */
    public function it_requires_a_valid_type_to_add_a_pack($type)
    {
        $this->store('member.pack.store', ['type' => $type], ['member' => $this->user->id])
            ->assertJsonMissingValidationErrors('type');
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
