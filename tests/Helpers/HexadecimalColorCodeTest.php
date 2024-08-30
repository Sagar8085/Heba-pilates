<?php

namespace Tests\Helpers;

use App\Constants\CreditPacks;
use App\Constants\PromotionConversionTypes;
use App\Constants\PurchaseTypes;
use App\Helpers\HexadecimalColorCode;
use Tests\TestCase;

/**
 * Class HexadecimalColorCodeTest
 *
 * @package Tests\Helpers
 */
class HexadecimalColorCodeTest extends TestCase
{
    /**
     * @test
     * @dataProvider orderables
     */
    public function it_returns_a_hexadecimal_color_depending_on_the_orderable(string $orderable_type, string $hex): void
    {
        $this->assertEquals($hex, HexadecimalColorCode::get($orderable_type));
    }

    /** @test */
    public function it_returns_a_random_hexadecimal_if_the_orderable_is_not_recognised(): void
    {
        $this->assertContains(
            HexadecimalColorCode::get('unrecognised orderable'),
            collect(config('hexadecimal_colours'))->pluck('colour')
        );
    }

    public function orderables(): array
    {
        return [
            [
                PurchaseTypes::PROMO_PURCHASES,
                '#475fc6',
            ],
            [
                PurchaseTypes::SUBSCRIPTION_PURCHASES,
                '#adc911',
            ],
            [
                PurchaseTypes::NEW_SUBSCRIPTION_PURCHASES,
                '#16b5c8',
            ],
            [
                PurchaseTypes::CREDIT_PACK_PURCHASES,
                '#8860ae',
            ],
            [
                PurchaseTypes::FREE_CREDIT_PLANS,
                '#ffad89',
            ],
            [
                PurchaseTypes::VIP_CREDIT_PLANS,
                '#ffda88',
            ],
            [
                PurchaseTypes::CORPORATE_CREDIT_PLANS,
                '#ef5998',
            ],
            [
                CreditPacks::THIRTY_PACK,
                '#ef5998',
            ],
            [
                CreditPacks::TEN_PACK,
                '#8860ae',
            ],
            [
                CreditPacks::ONE_CREDIT,
                '#ffad89',
            ],
            [
                CreditPacks::FREE_SESSION,
                '#17B5C8',
            ],
            [
                PromotionConversionTypes::NO_CONVERSION,
                '#17B5C8',
            ],
            [
                PromotionConversionTypes::CONVERTED_TO_SUBSCRIPTION,
                '#ef5998',
            ],
            [
                PromotionConversionTypes::CONVERTED_TO_CREDIT_PACK,
                '#8860ae',
            ],
        ];
    }
}
