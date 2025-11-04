<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Создаем корневые категории
        $electronics = Category::create([
            'name' => 'Электроника',
            'slug' => 'electronics'
        ]);

        $clothing = Category::create([
            'name' => 'Одежда',
            'slug' => 'clothing'
        ]);

        $home = Category::create([
            'name' => 'Дом и сад',
            'slug' => 'home-garden'
        ]);

        $sports = Category::create([
            'name' => 'Спорт',
            'slug' => 'sports'
        ]);

        // Создаем подкатегории для электроники
        $smartphones = new Category(['name' => 'Смартфоны', 'slug' => 'smartphones']);
        $smartphones->appendToNode($electronics)->save();

        $laptops = new Category(['name' => 'Ноутбуки', 'slug' => 'laptops']);
        $laptops->appendToNode($electronics)->save();

        $headphones = new Category(['name' => 'Наушники', 'slug' => 'headphones']);
        $headphones->appendToNode($electronics)->save();

        // Создаем подкатегории для одежды
        $mensClothing = new Category(['name' => 'Мужская одежда', 'slug' => 'mens-clothing']);
        $mensClothing->appendToNode($clothing)->save();

        $womensClothing = new Category(['name' => 'Женская одежда', 'slug' => 'womens-clothing']);
        $womensClothing->appendToNode($clothing)->save();

        $shoes = new Category(['name' => 'Обувь', 'slug' => 'shoes']);
        $shoes->appendToNode($clothing)->save();
    }
}
