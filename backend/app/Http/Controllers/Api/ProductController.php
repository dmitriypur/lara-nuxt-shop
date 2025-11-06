<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::with(['categories', 'media'])
            ->where('active', true);

        // Filter by popular
        if ($request->boolean('popular')) {
            $query->orderBy('created_at', 'desc');
        } 
        // Sorting
        else if ($request->has('sort')) {
            $sortBy = $request->get('sort', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // Default sort
            $query->orderBy('created_at', 'desc');
        }

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Price range filter
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $limit = $request->get('limit');

        if ($limit) {
            $products = $query->limit((int)$limit)->get();
        } else {
            $products = $query->paginate($request->get('per_page', 12));
        }

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        if (!$product->active) {
            abort(404, 'Product not found');
        }

        // Если это вариант, загружаем данные родителя
        if ($product->parent_id) {
            $product->load([
                'parent.media',
                'parent.categories',
                'parent.baseAttributeValues.attribute',
            ]);
        } else {
            $product->load(['media', 'categories']);
        }

        // Текущие атрибуты и базовые атрибуты
        $product->load([
            'attributeValues.attribute',
            'baseAttributeValues.attribute',
        ]);

        // Варианты с атрибутами/медиа/категориями
        $product->load([
            'variants' => fn ($q) => $q
                ->with(['media', 'categories', 'attributeValues.attribute'])
                ->where('active', true)
        ]);

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

    public function related(Product $product): AnonymousResourceCollection
    {
        $relatedProducts = collect();

        $productToLoad = $product->parent_id ? $product->parent : $product;

        $productToLoad->load([
            'media',
            'categories',
            'variants' => fn ($q) => $q
                ->with(['media', 'categories', 'attributeValues.attribute'])
                ->where('active', true)
        ]);

        $relatedProducts->push($productToLoad);
        $productToLoad->variants->each(fn ($variant) => $relatedProducts->push($variant));

        // Убираем дубликаты и фильтруем только активные
        $uniqueProducts = $relatedProducts->unique('id')->where('active', true);

        return ProductResource::collection($uniqueProducts);
    }
}