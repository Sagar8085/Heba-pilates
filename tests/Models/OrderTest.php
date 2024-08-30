<?php

namespace Tests\Models;

use App\Collections\OrderCollection;
use App\Models\Order;
use Tests\TestCase;

/**
 * Class OrderTest
 *
 * @package Tests\Models
 */
class OrderTest extends TestCase
{
    /** @test */
    public function it_returns_an_order_collection(): void
    {
        $this->assertInstanceOf(OrderCollection::class, Order::all());
    }
}
