<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = 'Товары';
    
    protected static ?string $modelLabel = 'Товар';
    
    protected static ?string $pluralModelLabel = 'Товары';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\TextInput::make('slug')
                    ->disabled()
                    ->dehydrated(false)
                    ->label('Слаг (генерируется автоматически)'),
                Forms\Components\TextInput::make('sku')
                    ->label('Sku'),
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'name')
                    ->label('Категории')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return \App\Models\Category::orderBy('_lft')->get()->pluck('indented_name', 'id');
                    }),
            
                Forms\Components\TextInput::make('price')
                    ->label('Цена'),
                Forms\Components\TextInput::make('old_price')
                    ->label(' Старая цена'),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
            
                SpatieMediaLibraryFileUpload::make('images')->collection('images')->multiple(),
                Forms\Components\Toggle::make('active')
                    ->label('Активен')
                    ->default(true),
                Forms\Components\TextInput::make('meta.title')
                    ->label('Мета Title'),
                Forms\Components\TextInput::make('meta.description')
                    ->label('Мета Description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Название'),
                Tables\Columns\TextColumn::make('slug')->label('Слаг'),
                Tables\Columns\TextColumn::make('description')->label('Описание'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
