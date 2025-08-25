<?php

namespace Webkul\Product\Filament\Resources\PriceListResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Product\Filament\Resources\PriceListResource;

class ViewPriceList extends ViewRecord
{
    protected static string $resource = PriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
