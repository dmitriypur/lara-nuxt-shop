<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Получить все категории в виде дерева
     */
    public function index(): JsonResponse
    {
        $categories = Category::with('children')->whereIsRoot()->defaultOrder()->get();
        
        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Получить только корневые категории
     */
    public function roots(): AnonymousResourceCollection
    {
        $categories = Category::whereIsRoot()->defaultOrder()->get();
        
        return CategoryResource::collection($categories);
    }

    /**
     * Получить категорию по slug
     */
    public function show(string $slug): CategoryResource
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        return new CategoryResource($category);
    }

    /**
     * Получить дочерние категории
     */
    public function children(string $slug): AnonymousResourceCollection
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $children = $category->children()->defaultOrder()->get();
        
        return CategoryResource::collection($children);
    }

    /**
     * Получить путь к категории (хлебные крошки)
     */
    public function breadcrumbs(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $ancestors = $category->ancestors()->get();
        $ancestors->push($category);
        
        return response()->json([
            'data' => CategoryResource::collection($ancestors),
        ]);
    }

    /**
     * Получить товары категории и её подкategorий
     */
    public function products(string $slug, Request $request): AnonymousResourceCollection
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Получаем ID категории и всех её потомков
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        
        $query = Product::with(['categories'])
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });

        // Фильтр по цене
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Сортировка
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['name', 'price', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate($request->get('per_page', 15));

        return ProductResource::collection($products);
    }

    /**
     * Поиск категорий
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $query = $request->get('q', '');
        
        $categories = Category::where('name', 'LIKE', "%{$query}%")
            ->orWhere('slug', 'LIKE', "%{$query}%")
            ->defaultOrder()
            ->limit(20)
            ->get();
        
        return CategoryResource::collection($categories);
    }
}