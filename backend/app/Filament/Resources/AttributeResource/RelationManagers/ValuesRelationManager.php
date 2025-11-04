<?php

namespace App\Filament\Resources\AttributeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';
    protected static ?string $recordTitleAttribute = 'value';
    protected static ?string $title = 'Значения атрибута';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('value')
                ->label('Значение')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')
                ->label('Слаг')->required()->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('value')->label('Значение')->searchable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
            ])
            ->headerActions([ Tables\Actions\CreateAction::make() ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([ Tables\Actions\DeleteBulkAction::make() ]);
    }
}
