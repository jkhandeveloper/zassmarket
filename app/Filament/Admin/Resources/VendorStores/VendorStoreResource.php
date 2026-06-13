<?php

namespace App\Filament\Admin\Resources\VendorStores;

use App\Filament\Admin\Resources\VendorStores\Pages\ManageVendorStores;
use App\Models\VendorStore;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VendorStoreResource extends Resource
{
    protected static ?string $model = VendorStore::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static UnitEnum|string|null $navigationGroup = 'Marketplace';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('owner_id')->relationship('owner', 'email')->searchable()->required(),
            Select::make('subscription_plan_id')->relationship('plan', 'name')->searchable()->required(),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            Select::make('status')->options([
                VendorStore::STATUS_PENDING => 'Pending',
                VendorStore::STATUS_APPROVED => 'Approved',
                VendorStore::STATUS_REJECTED => 'Rejected',
            ])->required(),
            TextInput::make('support_email')->email()->maxLength(255),
            TextInput::make('phone')->maxLength(40),
            DateTimePicker::make('approved_at'),
            Textarea::make('description')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('owner.email')->label('Owner')->searchable(),
                TextColumn::make('plan.name')->label('Plan')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageVendorStores::route('/')];
    }
}
