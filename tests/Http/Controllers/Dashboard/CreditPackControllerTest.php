<?php

namespace Tests\Http\Controllers\Dashboard;

use App\Models\CreditPack;
use Tests\TestCase;

/**
 * Class CreditPackTypeControllerTest
 *
 * @package Tests\Http\Controllers\Dashboard
 */
class CreditPackControllerTest extends TestCase
{
    /** @test */
    public function it_cannot_be_accessed_if_unauthorised(): void
    {
        $this->getJson(route('credit-pack.index'))->assertUnauthorized();
    }

    /** @test */
    public function it_gets_a_list_of_all_credit_pack_type(): void
    {
        $creditPack = $this->createCreditPack(2);

        $this->signIn()
            ->getJson(route('credit-pack.index'))
            ->assertJson(
                $creditPack->map(fn (CreditPack $creditPack) => [
                    'id' => $creditPack->id,
                    'name' => $creditPack->name,
                    'price' => $creditPack->price,
                ])->toArray()
            );
    }
}
