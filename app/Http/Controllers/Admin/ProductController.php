<?php

namespace App\Http\Controllers\Admin;

use App\Filters\ProductFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\Index;
use App\Http\Requests\Products\Store;
use App\Http\Requests\Products\Update;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ProductController
 *
 * @package App\Http\Controllers\Admin
 */
class ProductController extends Controller
{
    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function index(Index $request, ProductFilter $filters): AnonymousResourceCollection
    {
        return ProductResource::collection(
            Product::filter($filters)->paginate()
        );
    }

    public function store(Store $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            __('product.create', ['name' => $product->name]),
        ]);
    }

    public function update(Update $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            __('product.update', ['name' => $product->name]),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            __('product.delete', ['name' => $product->name]),
        ]);
    }
}
