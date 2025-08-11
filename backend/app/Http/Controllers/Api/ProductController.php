<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::with(['categories', 'variants', 'media'])
            ->where('active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Price range filter
        if ($request->has('price_min')) {
            $query->where('price_min', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('price_max', '<=', $request->price_max);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate($request->get('per_page', 12));

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        if (!$product->active) {
            abort(404, 'Product not found');
        }

        $product->load(['categories', 'variants' => function ($query) {
            $query->where('active', true);
        }, 'media']);

        return new ProductResource($product);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return ProductResource::collection(collect());
        }

        $products = Product::search($query)
            ->where('active', true)
            ->with(['categories', 'variants', 'media'])
            ->paginate($request->get('per_page', 12));

        return ProductResource::collection($products);
    }
}