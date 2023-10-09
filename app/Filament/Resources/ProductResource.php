<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\Section as ComponentsSection;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $label = 'Produtos';
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Imagems')
                        ->icon('heroicon-m-photo')
                        ->schema([
                            Forms\Components\FileUpload::make('images')
                                ->multiple()
                                ->image()
                                ->imageEditor()
                        ]),
                    Step::make('Categoria e Marca')
                        ->icon('heroicon-m-shopping-bag')
                        ->schema([
                            Forms\Components\Select::make('category_id')
                                ->searchable()
                                ->preload()
                                ->relationship('category', 'name', fn($query) => $query->orderBy('name'))
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->columnSpan('full')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('description')->rows(5)->columnSpan('full'),
                                    Forms\Components\FileUpload::make('image')->columnSpan('full')
                                        ->image(),
                                ]),
                            Forms\Components\Select::make('brand_id')
                                ->searchable()
                                ->preload()
                                ->relationship('brand', 'name', fn($query) => $query->orderBy('name'))
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->columnSpan('full')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('description')->rows(5)->columnSpan('full'),
                                    Forms\Components\FileUpload::make('image')->columnSpan('full')
                                        ->image(),
                                ]),
                        ]),
                    Step::make('Produto')
                        ->icon('heroicon-s-shopping-cart')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(60),
                            Forms\Components\TextInput::make('price')
                                ->required()
                                ->maxLength(10),
                            Forms\Components\TextInput::make('ean')
                                ->required()
                                ->maxLength(13),
                            Forms\Components\TextInput::make('sku')
                                ->label('SKU')
                                ->required()
                                ->maxLength(20),
                            Forms\Components\TextInput::make('model')
                                ->required()
                                ->maxLength(120),
                            Forms\Components\TextInput::make('quantity')
                                ->required()
                                ->numeric(),
                        ]),
                    Step::make('Descição')
                        ->icon('heroicon-o-clipboard')
                        ->schema([
                            Forms\Components\Textarea::make('description')
                                ->required()
                                ->columnSpanFull()
                                ->rows(5),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('category.name')->label('Categoria'),
                Tables\Columns\TextColumn::make('brand.name')->label('Marca'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ean')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->hidden()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make([

                    TextEntry::make('sku')
                        ->hiddenLabel()
                        ->size('lg')
                        ->weight('bold')
                        ->badge()
                        ->color('secondary'),

                    TextEntry::make('name')
                        ->size('lg')
                        ->weight('bold')
                        ->hiddenLabel(),

                    TextEntry::make('description')
                        ->label('Descrição'),

                    ImageEntry::make('images')->view('product-image'),
                ])
                    ->columnSpan(2),
                ComponentsSection::make([

                    TextEntry::make('price')
                        ->money('BRL')
                        ->hiddenLabel()
                        ->size('text-2xl')
                        ->alignCenter()
                        ->columnSpan(2)
                        ->weight('bold'),

                    TextEntry::make('model')
                        ->columnSpan(2)
                        ->label('Modelo'),

                    TextEntry::make('category.name')
                        ->label('Modelo')
                        ->color('info'),

                    TextEntry::make('brand.name')
                        ->label('Marca')
                        ->color('info'),

                    TextEntry::make('ean')
                        ->label('EAN'),



                    TextEntry::make('quantity')
                        ->label('Quantidade')
                        ->color('danger')

                ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('created_at', 'desc')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
