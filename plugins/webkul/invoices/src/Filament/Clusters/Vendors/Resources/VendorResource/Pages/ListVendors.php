<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Filament\Actions;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListVendors;

class ListVendors extends BaseListVendors
{
    protected static string $resource = VendorResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('invoices::filament/clusters/vendors/resources/vendor/pages/list-vendor.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('invoices::filament/clusters/vendors/resources/vendor/pages/list-vendor.header-actions.create.title'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
