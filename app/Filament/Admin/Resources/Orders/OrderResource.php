<?php

namespace App\Filament\Admin\Resources\Orders;

use App\Filament\Admin\Resources\Orders\Pages\ManageOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptRefund;

    protected static UnitEnum|string|null $navigationGroup = 'Sales';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('vendor_store_id')->relationship('vendorStore', 'name')->searchable()->required(),
            Select::make('customer_id')->relationship('customer', 'email')->searchable(),
            TextInput::make('order_number')->required()->maxLength(255),
            Select::make('status')->options([
                'pending' => 'Pending',
                'processing' => 'Processing',
                'shipped' => 'Shipped',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ])->required(),
            Select::make('payment_status')->options([
                'unpaid' => 'Unpaid',
                'cod_pending' => 'COD Pending',
                'paid' => 'Paid',
                'refunded' => 'Refunded',
            ])->required(),
            Select::make('payment_method')->options([
                'cod' => 'Cash on delivery',
                'card' => 'Card',
            ])->required(),
            TextInput::make('stripe_checkout_session_id')->maxLength(255),
            TextInput::make('customer_name')->required()->maxLength(255),
            TextInput::make('customer_email')->email()->required()->maxLength(255),
            TextInput::make('customer_phone')->maxLength(40),
            Textarea::make('shipping_address')->required()->columnSpanFull(),
            TextInput::make('subtotal_cents')->numeric()->required(),
            TextInput::make('shipping_cents')->numeric()->required(),
            TextInput::make('total_cents')->numeric()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->searchable()->sortable(),
                TextColumn::make('vendorStore.name')->label('Vendor')->sortable(),
                TextColumn::make('customer_email')->searchable(),
                TextColumn::make('payment_method')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('payment_status')->badge()->sortable(),
                TextColumn::make('total_cents')->money('USD', divideBy: 100)->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageOrders::route('/')];
    }
}
