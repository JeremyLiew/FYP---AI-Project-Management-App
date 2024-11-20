<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                MarkdownEditor::make('description')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->disableToolbarButtons([
                        'attachFiles'
                ]),
                Section::make('Thumbnail')
                ->description('Main images to be shown on product page')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('front')
                    ->image()
                    ->required()
                    ->disk('uploads')
                    ->collection('products-front')
                    ->directory('products')
                    ->maxSize(1024)
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1'),
                    SpatieMediaLibraryFileUpload::make('back')
                    ->image()
                    ->required()
                    ->disk('uploads')
                    ->collection('products-back')
                    ->directory('products')
                    ->maxSize(1024)
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1'),
                ])->columns(2),

                Section::make('Product images')
                ->description('Additional product image on product page')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('images')
                    ->image()
                    ->disk('uploads')
                    ->collection('products-images')
                    ->directory('products')
                    ->maxSize(1024)
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->multiple()
                ])->columnSpanFull(),
                Section::make('Available Sizes')
                ->description('Arrange the sizes accordingly to how you want the sizes to be displayed on product page , from the smallest size to the largest')
                ->schema([
                    Repeater::make('Available sizes')
                    ->relationship('sizes')
                    ->schema([
                        Select::make('size')
                        ->options([
                            'XS' => 'XS',
                            'S' => 'S',
                            'M' => 'M',
                            'L' => 'L',
                            'XL' => 'XL',
                        ])
                        ->required(),
                        TextInput::make('quantity')->numeric()->label('Quantity')->rules('required|integer|min:1'),
                    ])->columnSpanFull()
                    ->reorderableWithButtons()
                    ->addActionLabel('Add size')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                SpatieMediaLibraryImageColumn::make('front')
                ->collection('products-front'),
                SpatieMediaLibraryImageColumn::make('back')
                ->collection('products-back'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
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
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
