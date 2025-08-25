<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Filament\Pages\SubNavigationPosition;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill as BaseEditBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;

class EditBill extends BaseEditBill
{
    protected static string $resource = BillResource::class;

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
