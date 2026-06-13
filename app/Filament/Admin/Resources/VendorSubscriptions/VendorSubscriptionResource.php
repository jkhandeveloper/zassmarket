<?php

namespace App\Filament\Admin\Resources\VendorSubscriptions;

use App\Filament\Admin\Resources\VendorSubscriptions\Pages\ManageVendorSubscriptions;
use App\Models\VendorSubscription;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VendorSubscriptionResource extends Resource
{
    protected static ?string $model = VendorSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static UnitEnum|string|null $navigationGroup = 'Marketplace';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('vendor_store_id')->relationship('vendorStore', 'name')->searchable()->required(),
            Select::make('subscription_plan_id')->relationship('plan', 'name')->searchable()->required(),
            Select::make('status')->options([
                'active' => 'Active',
                'cancelled' => 'Cancelled',
                'expired' => 'Expired',
            ])->required(),
            Select::make('payment_method')->options([
                'card' => 'Card',
                'bank_transfer' => 'Bank transfer',
            ]),
            Select::make('payment_status')->options([
                'unpaid' => 'Unpaid',
                'pending' => 'Pending',
                'pending_review' => 'Pending review',
                'paid' => 'Paid',
            ])->required(),
            TextInput::make('stripe_checkout_session_id')->maxLength(255),
            TextInput::make('bank_receipt_path')->maxLength(255),
            DateTimePicker::make('paid_at'),
            DateTimePicker::make('starts_at'),
            DateTimePicker::make('ends_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendorStore.name')->label('Vendor')->searchable()->sortable(),
                TextColumn::make('plan.name')->label('Plan')->sortable(),
                TextColumn::make('payment_method')->badge()->sortable(),
                TextColumn::make('payment_status')->badge()->sortable(),
                TextColumn::make('bank_receipt_path')->label('Receipt')->searchable(),
                TextColumn::make('paid_at')->dateTime()->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageVendorSubscriptions::route('/')];
    }
}
