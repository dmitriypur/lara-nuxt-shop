<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Добавляем parent_id для отображения в форме
        $data['parent_id'] = $this->record->parent_id;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newParentId = $data['parent_id'] ?? null;
        $currentParentId = $this->record->parent_id;
        
        // Если родитель изменился, устанавливаем новый parent_id
        if ($newParentId !== $currentParentId) {
            $data['parent_id'] = $newParentId;
        }
        
        return $data;
    }
}
