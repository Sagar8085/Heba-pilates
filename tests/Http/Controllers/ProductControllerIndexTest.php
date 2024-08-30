<?php

namespace Tests\Http\Controllers;

use App\Models\Product;

/**
 * Class ProductControllerIndexTest
 *
 * @package Tests\Http\Controllers
 */
class ProductControllerIndexTest extends ControllerTest
{
    /** @test */
    public function it_cannot_be_accessed_if_unauthorised(): void
    {
        $this->getJson(route('product.index'))->assertUnauthorized();
    }

    /** @test */
    public function it_gets_a_list_of_paginated_products(): void
    {
        $products = Product::factory()
            ->count(15)
            ->create();

        $this->index('product.index')
            ->assertJson([
                'data' => $products->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'active' => $product->active,
                    'created_at' => $product->created_at->format('d/m/Y'),
                    'updated_at' => $product->updated_at->format('d/m/Y'),
                ])->toArray(),
                'links' => [
                    'first' => 'http://localhost/api/admin/product?page=1',
                    'last' => 'http://localhost/api/admin/product?page=1',
                    'prev' => null,
                    'next' => null,
                ],
                'meta' => [
                    'current_page' => 1,
                    'from' => 1,
                    'last_page' => 1,
                    'links' => [
                        [
                            'url' => null,
                            'label' => '&laquo; Previous',
                            'active' => false,
                        ],
                        [
                            'url' => 'http://localhost/api/admin/product?page=1',
                            'label' => '1',
                            'active' => true,
                        ],
                        [
                            'url' => null,
                            'label' => 'Next &raquo;',
                            'active' => false,
                        ],
                    ],
                    'path' => 'http://localhost/api/admin/product',
                    'per_page' => 15,
                    'to' => 15,
                    'total' => 15,
                ],
            ]);
    }

    /** @test */
    public function it_filters_a_list_of_paginated_products(): void
    {
        Product::factory()
            ->count(14)
            ->create();

        /** @var Product $product */
        $product = Product::factory()
            ->create([
                'name' => 'Gym Pass 5,000',
                'price' => 10000,
                'active' => true,
            ]);

        $this->index('product.index', [
            'search' => 'Gym Pass 5,000',
        ])->assertJson([
            'data' => [
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'active' => $product->active,
                    'created_at' => $product->created_at->format('d/m/Y'),
                    'updated_at' => $product->updated_at->format('d/m/Y'),
                ],
            ],
            'links' => [
                'first' => 'http://localhost/api/admin/product?page=1',
                'last' => 'http://localhost/api/admin/product?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/product?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/product',
                'per_page' => 15,
                'to' => 1,
                'total' => 1,
            ],
        ]);
    }
}
