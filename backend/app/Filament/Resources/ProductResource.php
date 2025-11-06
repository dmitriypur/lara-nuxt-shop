<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Товары';

    protected static ?string $modelLabel = 'Товар';

    protected static ?string $pluralModelLabel = 'Товары';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNull('parent_id');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'title')
                    ->label('Родительский товар'),
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

                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->label('Изображения')
                    ->responsiveImages()
                    ->multiple(),
                Forms\Components\Toggle::make('active')
                    ->label('Активен')
                    ->default(true),
                Forms\Components\TextInput::make('meta.title')
                    ->label('Мета Title'),
                Forms\Components\TextInput::make('meta.description')
                    ->label('Мета Description'),
                Forms\Components\Section::make('Характеристики')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema(function (Forms\Get $get, ?Product $record) {
                                $selectedCategoryIds = $get('categories') ?? [];
                                // Если запись существует и у неё есть категории, используем их приоритетно
                                if ($record && $record->exists && empty($selectedCategoryIds)) {
                                    $selectedCategoryIds = $record->categories()->pluck('categories.id')->toArray();
                                }

                                // Если категорий нет — не показываем характеристики
                                if (empty($selectedCategoryIds)) {
                                    return [
                                        Forms\Components\Placeholder::make('no_categories')
                                            ->label('')
                                            ->content('Выберите категории — и здесь появятся доступные характеристики.'),
                                    ];
                                }

                                // Собираем ID выбранных категорий и всех их предков (наследование атрибутов)
                                $allCategoryIds = [];
                                $categories = Category::whereIn('id', $selectedCategoryIds)->get();
                                foreach ($categories as $cat) {
                                    $ancestorIds = $cat->ancestors()->pluck('id')->toArray();
                                    $allCategoryIds = array_merge($allCategoryIds, $ancestorIds, [$cat->id]);
                                }
                                $allCategoryIds = array_values(array_unique($allCategoryIds));

                                // Берём атрибуты, привязанные к любой из этих категорий
                                $attributes = Attribute::whereHas('categories', function ($q) use ($allCategoryIds) {
                                        $q->whereIn('categories.id', $allCategoryIds);
                                    })
                                    ->with('values')
                                    ->orderBy('name')
                                    ->get();

                                // Строим компоненты для каждого атрибута
                                $components = [];
                                foreach ($attributes as $attribute) {
                                    $components[] = Forms\Components\Fieldset::make($attribute->name)
                                        ->schema([
                                            Forms\Components\CheckboxList::make('attr_' . $attribute->id)
                                                ->label('')
                                                ->options($attribute->values->pluck('value', 'id')->toArray())
                                                ->columns(3)
                                                ->dehydrated(false)
                                                ->default(function ($record) use ($attribute) {
                                                    if (!$record) {
                                                        return [];
                                                    }
                                                    return $record->baseAttributeValues()
                                                        ->where('attribute_id', $attribute->id)
                                                        ->pluck('attribute_values.id')
                                                        ->toArray();
                                                }),
                                        ]);
                                }

                                return $components;
                            }),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->collection('images')
                    ->label('Изображение')
                    ->size(60)
                    ->circular(false),
                Tables\Columns\TextColumn::make('title')->label('Название')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('Слаг'),
                Tables\Columns\TextColumn::make('price')->label('Цена')->money('RUB'),
                Tables\Columns\IconColumn::make('active')->label('Активен')->boolean(),
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
            RelationManagers\ProductVariantsRelationManager::class,
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
