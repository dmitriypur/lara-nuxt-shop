<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    
    protected static ?string $title = 'Создать товар';

    protected function afterCreate(): void
    {
        // Синхронизируем значения атрибутов базового товара из чекбоксов
        // ВАЖНО: используем getRawState(), чтобы получить значения полей с dehydrated(false)
        $state = $this->form->getRawState();
        $ids = [];
        foreach ($state as $key => $value) {
            if (Str::startsWith($key, 'attr_') && is_array($value)) {
                $ids = array_merge($ids, array_filter($value));
            }
        }

        $ids = array_values(array_unique($ids));
        // Пишем base
        $this->record->baseAttributeValues()->syncWithPivotValues($ids, ['type' => 'base']);
        // И сразу делаем selected источником истины для фронта
        $this->record->attributeValues()->syncWithPivotValues($ids, ['type' => 'selected']);
    }
}
