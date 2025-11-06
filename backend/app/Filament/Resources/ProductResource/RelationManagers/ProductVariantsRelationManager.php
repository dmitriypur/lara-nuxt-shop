<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Attribute;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        $parent = $this->getOwnerRecord();

        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->default($parent?->title),
                Forms\Components\TextInput::make('slug')
                    ->disabled()
                    ->dehydrated(false)
                    ->label('Слаг (генерируется автоматически)'),
                Forms\Components\TextInput::make('sku')
                    ->label('Sku')
                    ->maxLength(255)
                    ->default($parent?->sku),
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'name')
                    ->label('Категории')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->options(function () {
                        return \App\Models\Category::orderBy('_lft')->get()->pluck('indented_name', 'id');
                    })
                    ->default(function () use ($parent) {
                        return $parent?->categories?->pluck('id')?->toArray() ?? [];
                    }),
                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->default($parent?->price),
                Forms\Components\TextInput::make('old_price')
                    ->label('Старая цена')
                    ->default(null),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->default($parent?->description),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->label('Изображения')
                    ->responsiveImages()
                    ->multiple(),
                Forms\Components\Toggle::make('active')
                    ->label('Активен')
                    ->default($parent?->active ?? true),
                Forms\Components\TextInput::make('meta.title')
                    ->label('Мета Title')
                    ->default($parent?->meta['title'] ?? null),
                Forms\Components\TextInput::make('meta.description')
                    ->label('Мета Description')
                    ->default($parent?->meta['description'] ?? null),
                Forms\Components\Section::make('Характеристики варианта')
                    ->schema([
                        Forms\Components\Hidden::make('attributeValuesSync')
                            ->dehydrated(true)
                            ->default(function ($record) {
                                if (!$record) {
                                    return [];
                                }
                                return $record->attributeValues()->pluck('attribute_values.id')->toArray();
                            }),
                        Forms\Components\Grid::make(3)
                            ->schema(function (Forms\Get $get, $record) use ($parent) {
                                // Выбранные категории варианта; если пусто — категории родителя
                                $selectedCategoryIds = $get('categories') ?? [];
                                if (empty($selectedCategoryIds)) {
                                    $selectedCategoryIds = ($record?->categories()->pluck('categories.id')->toArray())
                                        ?: ($parent?->categories()->pluck('categories.id')->toArray() ?? []);
                                }

                                if (empty($selectedCategoryIds)) {
                                    return [
                                        Forms\Components\Placeholder::make('no_categories')
                                            ->label('')
                                            ->content('Выберите категории варианта или у родителя — появятся доступные характеристики.'),
                                    ];
                                }

                                // Категории + предки (наследование атрибутов)
                                $allCategoryIds = [];
                                $categories = Category::whereIn('id', $selectedCategoryIds)->get();
                                foreach ($categories as $cat) {
                                    $ancestorIds = $cat->ancestors()->pluck('id')->toArray();
                                    $allCategoryIds = array_merge($allCategoryIds, $ancestorIds, [$cat->id]);
                                }
                                $allCategoryIds = array_values(array_unique($allCategoryIds));

                                // Атрибуты, привязанные к этим категориям
                                $attributes = Attribute::whereHas('categories', function ($q) use ($allCategoryIds) {
                                        $q->whereIn('categories.id', $allCategoryIds);
                                    })
                                    ->with('values')
                                    ->orderBy('name')
                                    ->get();

                                $attributeIds = $attributes->pluck('id')->toArray();

                                // Формируем компоненты
                                $components = [];
                                foreach ($attributes as $attribute) {
                                    $components[] = Forms\Components\Fieldset::make($attribute->name)
                                        ->schema([
                                            Forms\Components\CheckboxList::make('attr_' . $attribute->id)
                                                ->label('')
                                                ->options($attribute->values->pluck('value', 'id')->toArray())
                                                ->columns(3)
                                                ->dehydrated(false)
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($attributeIds) {
                                                    $ids = [];
                                                    foreach ($attributeIds as $id) {
                                                        $vals = (array) ($get('attr_' . $id) ?? []);
                                                        $ids = array_merge($ids, array_filter($vals));
                                                    }
                                                    $set('attributeValuesSync', array_values(array_unique($ids)));
                                                })
                                                ->default(function ($record) use ($attribute) {
                                                    if (!$record) {
                                                        return [];
                                                    }
                                                    return $record->attributeValues()
                                                        ->where('attribute_id', $attribute->id)
                                                        ->pluck('attribute_values.id')
                                                        ->toArray();
                                                }),
                                        ]);
                                }

                                return $components;
                            }),
                    ])
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('sku'),
                Tables\Columns\TextColumn::make('price')->money('rub'),
                Tables\Columns\IconColumn::make('active')->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record, array $data) {
                        $record->attributeValues()->sync($data['attributeValuesSync'] ?? []);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record, array $data) {
                        $record->attributeValues()->sync($data['attributeValuesSync'] ?? []);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}