<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\AttributeResource\RelationManagers\ValuesRelationManager;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Атрибуты';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Тип')
                    ->required()
                    ->options([
                            'text' => 'Текст',
                            'number' => 'Число',
                            'select' => 'Выпадающий список',
                            'color' => 'Цвет',
                        ]),
                Forms\Components\Select::make('categories')
                    ->label('Категории, где доступен атрибут')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return \App\Models\Category::orderBy('_lft')->get()->pluck('indented_name', 'id');
                    })
                    ->helperText('Если выбрана родительская категория, атрибут будет доступен и в её дочерних категориях.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('categories_count')
                    ->counts('categories')
                    ->label('Категорий')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
           ValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
}
