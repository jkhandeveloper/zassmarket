<?php

namespace App\Filament\Admin\Resources\VendorStores\Pages;

use App\Filament\Admin\Resources\VendorStores\VendorStoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageVendorStores extends ManageRecords
{
    protected static string $resource = VendorStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
