<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BrandResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $label = 'Marcas';
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->columnSpan('full')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')->rows(5)->columnSpan('full'),
                Forms\Components\FileUpload::make('image')->columnSpan('full')
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Marca')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('description')
                    ->hidden()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')->label('Imagem'),
                Tables\Columns\TextColumn::make('products_count')
                    ->sortable()
                    ->counts('products')
                    ->label('Produtos'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i')
                    ->label('Criado em')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/y H:i')
                    ->label('Atualizado em')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('created_at')
                    ->label('Criado em')
                    ->placeholder('Selecione uma opção')
                    ->trueLabel('Crescente')
                    ->falseLabel('Decrescente')
                    ->queries(
                        true: fn(Builder $query) => $query->orderBy('created_at'),
                        false: fn(Builder $query) => $query->orderBy('created_at', 'desc'),
                        blank: fn(Builder $query) => $query->orderBy('created_at')
                    ),
                Tables\Filters\TernaryFilter::make('name')
                    ->label('Marca')
                    ->placeholder('Selecione uma opção')
                    ->trueLabel('Crescente')
                    ->falseLabel('Decrescente')
                    ->queries(
                        true: fn(Builder $query) => $query->orderBy('name'),
                        false: fn(Builder $query) => $query->orderBy('name', 'desc'),
                        blank: fn(Builder $query) => $query->orderBy('name')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBrands::route('/'),
        ];
    }
}
