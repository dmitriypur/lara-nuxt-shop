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
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('name')->get();
        
        return CategoryResource::collection($categories);
    }

    /**
     * Получить только корневые категории
     */
    public function roots(): AnonymousResourceCollection
    {
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        
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
        
        $query = Product::with(['categories', 'media', 'variants'])
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });

        // Поиск по названию
        if ($request->has('search') && $request->get('search')) {
            $search = $request->get('search');
            $query->where('title', 'LIKE', "%{$search}%");
        }

        // Фильтр по цене
        if ($request->has('price_from') && $request->get('price_from') !== '') {
            $query->where('price', '>=', $request->get('price_from'));
        }

        if ($request->has('price_to') && $request->get('price_to') !== '') {
            $query->where('price', '<=', $request->get('price_to'));
        }

        // Сортировка
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['title', 'price', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Фильтр по наличию на складе (используем колонку active)
        if ($request->has('in_stock') && $request->get('in_stock')) {
            $query->where('active', true);
        }

        $products = $query->paginate($request->get('per_page', 12));

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