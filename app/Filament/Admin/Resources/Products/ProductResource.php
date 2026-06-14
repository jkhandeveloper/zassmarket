<?php

namespace App\Filament\Admin\Resources\Products;

use App\Filament\Admin\Resources\Products\Pages\ManageProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static UnitEnum|string|null $navigationGroup = 'Catalog';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('vendor_store_id')->relationship('vendorStore', 'name')->searchable()->required(),
            Select::make('category_id')->relationship('category', 'name')->searchable(),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255),
            TextInput::make('price_cents')->numeric()->required(),
            TextInput::make('discount_percent')->numeric()->minValue(0)->maxValue(95)->default(0),
            TextInput::make('stock')->numeric()->required(),
            Toggle::make('is_active')->default(true),
            Textarea::make('description')->columnSpanFull(),
            TextInput::make('seo_title')->maxLength(255),
            Textarea::make('seo_description')->rows(3)->columnSpanFull(),
            TextInput::make('seo_keywords')->maxLength(255),
            Repeater::make('images')
                ->relationship()
                ->schema([
                    TextInput::make('path')
                        ->label('Image URL or public path')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('alt_text')
                        ->label('Alt text')
                        ->maxLength(255),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('show_on_homepage_hero')
                        ->label('Show in homepage hero')
                        ->helperText('Only selected images appear in the homepage hero slider.'),
                ])
                ->orderColumn('sort_order')
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('vendorStore.name')->label('Vendor')->sortable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('seo_title')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price_cents')->money('USD', divideBy: 100)->sortable(),
                TextColumn::make('discount_percent')->suffix('%')->sortable(),
                TextColumn::make('stock')->sortable(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageProducts::route('/')];
    }
}
