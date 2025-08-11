<?php

declare(strict_types=1);

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Создать категорию'),
        ];
    }

    public function getAdjacencyListRecords(): array
    {
        return Category::query()
            ->defaultOrder()
            ->get()
            ->toTree()
            ->toArray();
    }

    public function getAdjacencyListTitleAttribute(): string
    {
        return 'name';
    }

    public function getAdjacencyListChildrenAttribute(): string
    {
        return 'children';
    }
}
