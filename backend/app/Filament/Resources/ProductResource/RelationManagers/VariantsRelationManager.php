<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    
    protected static ?string $title = 'Варианты товара';
    
    protected static ?string $modelLabel = 'Вариант';
    
    protected static ?string $pluralModelLabel = 'Варианты';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->required()
                    ->numeric()
                    ->prefix('₽'),
                Forms\Components\TextInput::make('old_price')
                    ->label('Старая цена')
                    ->numeric()
                    ->prefix('₽'),
                Forms\Components\TextInput::make('sku')
                    ->label('Артикул')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('stock')
                    ->label('Количество на складе')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\Toggle::make('is_default')
                    ->label('По умолчанию'),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->label('Изображения')
                    ->responsiveImages()
                    ->multiple(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->collection('images')
                    ->label('Изображение')
                    ->size(60)
                    ->circular(false),
                Tables\Columns\TextColumn::make('sku')
                    ->label('Артикул')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('old_price')
                    ->label('Старая цена')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('На складе')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('По умолчанию')->boolean()->trueColor('info')->falseColor('warning')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
