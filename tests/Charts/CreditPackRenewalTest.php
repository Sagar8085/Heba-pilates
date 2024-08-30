<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\CreditPackRenewal;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class CreditPackRenewalTest
 *
 * @package Tests\Charts;
 */
class CreditPackRenewalTest extends TestCase
{
    /** @test */
    public function it_returns_the_count_totals(): void
    {
        $member = $this->createAdministrator()->memberProfile;

        $creditPack = $this->createCreditPack(null, [
            'promotional' => false,
        ]);

        $this->createOrderForCreditPack([
            'member_id' => $member->id,
            'expires' => Carbon::create(2021, 1, 26),
            'created_at' => Carbon::create(2021, 1, 25),
        ], 1, $creditPack);

        $orders = $this->createOrderForCreditPack([
            'value' => 10000,
            'member_id' => $member->id,
            'created_at' => Carbon::create(2021, 1, 31),
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'member_id' => $member->id,
            'expires' => Carbon::create(2021, 1, 26),
            'created_at' => Carbon::create(2021, 1, 26),
        ], 1, $creditPack);

        $orders = $orders->merge($this->createOrderForCreditPack([
            'value' => 20000,
            'member_id' => $member->id,
            'created_at' => Carbon::create(2021, 2),
        ], 1, $creditPack));

        $this->createOrderForCreditPack([
            'member_id' => $member->id,
            'expires' => Carbon::create(2021, 1, 26),
            'created_at' => Carbon::create(2021, 1, 27),
        ], 1, $creditPack);

        $orders = $orders->merge($this->createOrderForCreditPack([
            'value' => 30000,
            'member_id' => $member->id,
            'created_at' => Carbon::create(2021, 2, 2),
        ], 1, $creditPack));

        $this->createOrderForCreditPack([
            'member_id' => $member->id,
            'expires' => Carbon::create(2021, 1, 26),
            'created_at' => Carbon::create(2021, 1, 28),
        ], 1, $creditPack);

        $orders = $orders->merge($this->createOrderForCreditPack([
            'value' => 40000,
            'member_id' => $member->id,
            'created_at' => Carbon::create(2021, 2, 3),
        ], 1, $creditPack));

        $this->createOrderForCreditPack([
            'member_id' => $member->id,
            'expires' => Carbon::create(2021, 1, 26),
            'created_at' => Carbon::create(2021, 1, 29),
        ], 1, $creditPack);

        $orders = $orders->merge($this->createOrderForCreditPack([
            'value' => 50000,
            'member_id' => $member->id,
            'created_at' => Carbon::create(2021, 2, 4),
        ], 1, $creditPack));

        $this->createOrderForCreditPack([
            'member_id' => $member->id,
            'expires' => Carbon::create(2021, 1, 26),
            'created_at' => Carbon::create(2021, 1, 30),
        ], 1, $creditPack);

        $orders = $orders->merge($this->createOrderForCreditPack([
            'value' => 60000,
            'member_id' => $member->id,
            'created_at' => Carbon::create(2021, 2, 5),
        ], 1, $creditPack));

        $this->createOrderForCreditPack([
            'member_id' => $member->id,
            'expires' => Carbon::create(2021, 1, 26),
            'created_at' => Carbon::create(2021, 1, 31),
        ], 1, $creditPack);

        $orders = $orders->merge($this->createOrderForCreditPack([
            'value' => 70000,
            'member_id' => $member->id,
            'created_at' => Carbon::create(2021, 2, 6),
        ], 1, $creditPack));

        $this->createOrderForCreditPack([
            'created_at' => Carbon::create(2021, 2, 2),
        ], 20);

        $dataset = (new CreditPackRenewal($orders))
            ->setFrom(
                Carbon::create(2021, 1, 31)
            )
            ->setTo(
                Carbon::create(2021, 2, 06)
            )
            ->toChart();

        $this->assertEquals(
            [
                'chart' => [
                    'labels' => [
                        '31/01',
                        '01/02',
                        '02/02',
                        '03/02',
                        '04/02',
                        '05/02',
                        '06/02',
                    ],
                    'datasets' => [
                        0 => [
                            'label' => 'Credit Pack Renewals',
                            'data' => [
                                100,
                                200,
                                300,
                                400,
                                500,
                                600,
                                700,
                            ],
                        ],
                    ],
                ],
                'metrics' => [
                    0 => [
                        'value' => '£2,800.00 (7)',
                    ],
                ],
            ],
            $dataset
        );
    }
}