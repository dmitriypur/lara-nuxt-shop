<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Убираем parent_id из данных, так как мы будем использовать методы nested set
        $parentId = $data['parent_id'] ?? null;
        unset($data['parent_id']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $parentId = $this->form->getRawState()['parent_id'] ?? null;
        
        if ($parentId) {
            $parent = Category::find($parentId);
            if ($parent) {
                $this->record->appendToNode($parent)->save();
            }
        }
    }
}
