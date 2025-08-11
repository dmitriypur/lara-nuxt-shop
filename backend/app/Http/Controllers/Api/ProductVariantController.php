<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductVariantController extends Controller
{
    public function index(Product $product): AnonymousResourceCollection
    {
        if (!$product->active) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $variants = $product->variants()
            ->where('active', true)
            ->orderBy('price')
            ->get();

        return ProductVariantResource::collection($variants);
    }

    public function show(ProductVariant $variant): ProductVariantResource
    {
        if (!$variant->active || !$variant->product->active) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        $variant->load('product');

        return new ProductVariantResource($variant);
    }
}