<?php

namespace App\Filament\Admin\Resources\VendorSubscriptions\Pages;

use App\Filament\Admin\Resources\VendorSubscriptions\VendorSubscriptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageVendorSubscriptions extends ManageRecords
{
    protected static string $resource = VendorSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
