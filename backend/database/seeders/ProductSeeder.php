<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $electronics = Category::firstOrCreate(
            ['slug' => 'electronics'],
            ['name' => 'Электроника']
        );

        $clothing = Category::firstOrCreate(
            ['slug' => 'clothing'],
            ['name' => 'Одежда']
        );

        // Create products
        $products = [
            [
                'title' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'Новый iPhone 15 Pro с чипом A17 Pro',
                'price' => 99999.00,
                'old_price' => 109999.00,
                'sku' => 'IPHONE15PRO',
                'active' => true,
            ],
            [
                'title' => 'MacBook Pro 14"',
                'slug' => 'macbook-pro-14',
                'description' => 'MacBook Pro 14 дюймов с чипом M3',
                'price' => 199999.00,
                'sku' => 'MBP14M3',
                'active' => true,
            ],
            [
                'title' => 'Футболка базовая',
                'slug' => 'basic-tshirt',
                'description' => 'Базовая футболка из 100% хлопка',
                'price' => 1999.00,
                'sku' => 'TSHIRT001',
                'active' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );

            // Create variants for each product
            if ($product->title === 'iPhone 15 Pro') {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => 'IPHONE15PRO-128GB',
                    'price' => 99999.00,
                    'stock' => 30,
                ]);
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => 'IPHONE15PRO-256GB',
                    'price' => 109999.00,
                    'stock' => 20,
                ]);
            } elseif ($product->title === 'Футболка базовая') {
                $sizes = ['S', 'M', 'L', 'XL'];
                foreach ($sizes as $size) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => "TSHIRT001-{$size}",
                        'price' => 1999.00,
                        'stock' => 25,
                    ]);
                }
            }
        }
    }
}
