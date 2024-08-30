<?php

namespace Tests\Models;

use App\Models\CreditPack;
use Tests\TestCase;

class CreditPackTest extends TestCase
{
    /** @test */
    public function it_knows_which_credit_packs_are_non_promotional(): void
    {
        $this->createCreditPack(5, [
            'promotional' => false,
        ]);

        $this->createCreditPack(5, [
            'promotional' => true,
        ]);

        $this->assertCount(5, CreditPack::nonPromotional()->get());
    }

    /** @test */
    public function it_knows_which_credit_packs_are_promotional(): void
    {
        $this->createCreditPack(5, [
            'promotional' => false,
        ]);

        $this->createCreditPack(5, [
            'promotional' => true,
        ]);

        $this->assertCount(5, CreditPack::promotional()->get());
    }
}
