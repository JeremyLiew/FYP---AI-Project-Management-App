<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('order_status')
            ->options([
                'cancelled / refund' => 'cancelled / refund',
                'paid' => 'paid',
                'shipping' => 'shipping',
                'completed' => 'completed',
            ])->required(),
            Section::make('Orders')
            ->schema([
                Repeater::make('Products')
                    ->relationship('products') // Assuming 'products' is the name of the relationship in your Order model
                    ->schema([
                        TextInput::make('id')->label('Id')->disabled(),
                        TextInput::make('title')->label('Product Title')->disabled(),
                        TextInput::make('price')->label('Price')->disabled()->numeric()->prefix('$'),
                        TextInput::make('size')->label('Size')->disabled(),
                        SpatieMediaLibraryFileUpload::make('front')->columnSpanFull()
                            ->image()
                            ->required()
                            ->disk('uploads')
                            ->collection('products-front')
                            ->directory('products')
                            ->maxSize(1024)
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1'),
                    ])
                    ->columns(4)
                    ->reorderableWithButtons()
                    ->addActionLabel('Add Product')
                    ->disabled(), // Disable the entire repeater
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_status')
                    ->searchable(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
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
