<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
    
    protected static ?string $title = 'Редактировать товар';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Синхронизируем значения атрибутов базового товара из чекбоксов
        $state = $this->form->getState();
        $ids = [];
        foreach ($state as $key => $value) {
            if (Str::startsWith($key, 'attr_') && is_array($value)) {
                $ids = array_merge($ids, array_filter($value));
            }
        }

        $ids = array_values(array_unique($ids));
        $this->record->baseAttributeValues()->sync($ids);
    }
}
