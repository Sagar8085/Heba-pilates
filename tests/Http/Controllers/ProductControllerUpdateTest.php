<?php

namespace Tests\Http\Controllers;

use App\Models\Product;

/**
 * Class ProductControllerUpdateTest
 *
 * @package Tests\Http\Controllers
 */
class ProductControllerUpdateTest extends ControllerTest
{
    /** @test */
    public function it_successfully_updates_a_product(): void
    {
        $product = Product::factory()->create();

        $this->update(
            'product.update',
            $product,
            [
                'name' => 'Updated product name',
                'price' => '400.00',
                'active' => 0,
            ]
        );

        $this->assertDatabaseHas('products', [
            'name' => 'Updated product name',
            'price' => 40000,
            'active' => false,
        ]);
    }

    /** @test */
    public function it_gets_the_correct_response_when_updating_a_product(): void
    {
        $product = Product::factory()->create();

        $this->update(
            'product.update',
            $product,
            [
                'name' => 'Updated product name',
                'price' => '400.00',
                'active' => 0,
            ]
        )->assertJson([
            'Updated product name updated successfully',
        ]);
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        $product = Product::factory()->create();

        $this->update(
            'product.update',
            $product,
            [
                'name' => null,
                'price' => null,
                'active' => null,
            ]
        )->assertUnprocessable();
    }

    /** @test */
    public function it_validates_correctly_formatted_fields(): void
    {
        $product = Product::factory()->create();

        $this->update(
            'product.update',
            $product,
            [
                'name' => 11212,
                'price' => '212121',
                'active' => 'wqeqeqwe',
            ]
        )->assertUnprocessable();
    }

    /** @test */
    public function it_ignores_itself_when_validating_duplicated_product_names(): void
    {
        $product = $this->createProduct(null, [
            'name' => 'Original product name',
        ]);

        $this->update(
            'product.update',
            $product,
            [
                'name' => 'Original product name',
                'price' => 40000,
                'active' => false,
            ]
        )->assertOk();
    }
}
