<?php

namespace Tests\Http\Controllers;

/**
 * Class ProductControllerStoreTest
 *
 * @package Tests\Http\Controllers
 */
class ProductControllerStoreTest extends ControllerTest
{
    private function storeProduct(array $data)
    {
        return $this->store('product.store', $data);
    }

    /** @test */
    public function it_successfully_creates_a_product(): void
    {
        $this->storeProduct([
            'name' => 'Test product name',
            'price' => '200.00',
            'active' => 1,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test product name',
            'price' => 20000,
            'active' => true,
        ]);
    }

    /** @test */
    public function it_gets_the_correct_response_when_creating_a_product(): void
    {
        $this->storeProduct([
            'name' => 'Test product name',
            'price' => '200.00',
            'active' => 1,
        ])->assertJson([
            'Test product name created successfully',
        ]);
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        $this->storeProduct([
            'name' => null,
            'price' => null,
            'active' => null,
        ])->assertJsonValidationErrors([
            'name',
            'price',
            'active',
        ]);
    }

    /** @test */
    public function it_validates_correctly_formatted_fields(): void
    {
        $this->storeProduct([
            'name' => 11212,
            'price' => 'asss',
            'active' => 'wqeqeqwe',
        ])->assertJsonValidationErrors([
            'name',
            'price',
            'active',
        ]);
    }

    /** @test */
    public function it_validates_duplicated_product_names(): void
    {
        $this->createProduct(1, [
            'name' => 'Original product name',
        ]);

        $this->storeProduct([
            'name' => 'Original product name',
            'price' => 999900,
            'active' => 1,
        ])->assertJsonValidationErrors([
            'name',
        ]);
    }
}
