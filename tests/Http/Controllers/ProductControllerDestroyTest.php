<?php

namespace Tests\Http\Controllers;

use App\Models\Product;

/**
 * Class ProductControllerDestroyTest
 *
 * @package Tests\Http\Controllers
 */
class ProductControllerDestroyTest extends ControllerTest
{
    /** @test */
    public function it_successfully_deletes_a_product(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $this->destroy('product.destroy', $product);


        $this->assertDatabaseMissing('products', [
            'name' => $product->name,
            'price' => $product->price,
            'active' => $product->active,
        ]);
    }

    /** @test */
    public function it_gets_the_correct_response_when_deleting_a_product(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $this->destroy('product.destroy', $product)->assertJson([
            $product->name . ' deleted successfully',
        ]);
    }

    /** @test */
    public function it_cannot_delete_a_product_that_doesnt_exist(): void
    {
        $product = Product::factory()->create();

        $product->delete();

        $this->destroy('product.destroy', $product)->assertNotFound();
    }
}
