<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

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

        // Create parent products
        $parentProducts = [
            [
                'title'       => 'iPhone 15 Pro',
                'slug'        => 'iphone-15-pro',
                'description' => 'Новый iPhone 15 Pro с чипом A17 Pro',
                'price'       => 99999.00,
                'old_price'   => 109999.00,
                'sku'         => 'IPHONE15PRO',
                'active'      => true,
                'category'    => $electronics,
            ],
            [
                'title'       => 'MacBook Pro 14"',
                'slug'        => 'macbook-pro-14',
                'description' => 'MacBook Pro 14 дюймов с чипом M3',
                'price'       => 199999.00,
                'sku'         => 'MBP14M3',
                'active'      => true,
                'category'    => $electronics,
            ],
            [
                'title'       => 'Футболка базовая',
                'slug'        => 'basic-tshirt',
                'description' => 'Базовая футболка из 100% хлопка',
                'price'       => 1999.00,
                'sku'         => 'TSHIRT001',
                'active'      => true,
                'category'    => $clothing,
            ],
        ];

        foreach ($parentProducts as $productData) {
            $category = $productData['category'];
            unset($productData['category']);

            $product = Product::firstOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
            $product->categories()->sync([$category->id]);

            // Create variants
            if ($product->slug === 'iphone-15-pro') {
                $variants = [
                    [
                        'parent_id'   => $product->id,
                        'title'       => 'iPhone 15 Pro 128GB',
                        'slug'        => 'iphone-15-pro-128gb',
                        'description' => 'Новый iPhone 15 Pro 128GB с чипом A17 Pro',
                        'price'       => 99999.00,
                        'sku'         => 'IPHONE15PRO-128GB',
                        'stock'       => 30,
                        'active'      => true,
                    ],
                    [
                        'parent_id'   => $product->id,
                        'title'       => 'iPhone 15 Pro 256GB',
                        'slug'        => 'iphone-15-pro-256gb',
                        'description' => 'Новый iPhone 15 Pro 256GB с чипом A17 Pro',
                        'price'       => 109999.00,
                        'sku'         => 'IPHONE15PRO-256GB',
                        'stock'       => 20,
                        'active'      => true,
                    ],
                ];
                foreach ($variants as $variantData) {
                    $variant = Product::create($variantData);
                    $variant->categories()->sync([$category->id]);
                }
            } elseif ($product->slug === 'basic-tshirt') {
                $sizes = ['S', 'M', 'L', 'XL'];
                foreach ($sizes as $size) {
                    $variantData = [
                        'parent_id'   => $product->id,
                        'title'       => "Футболка базовая ({$size})",
                        'slug'        => "basic-tshirt-{$size}",
                        'description' => "Базовая футболка из 100% хлопка, размер {$size}",
                        'price'       => 1999.00,
                        'sku'         => "TSHIRT001-{$size}",
                        'stock'       => 25,
                        'active'      => true,
                    ];
                    $variant = Product::create($variantData);
                    $variant->categories()->sync([$category->id]);
                }
            }
        }
    }
}
