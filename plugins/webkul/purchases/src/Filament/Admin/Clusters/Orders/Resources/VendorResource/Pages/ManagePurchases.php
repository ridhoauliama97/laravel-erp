<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;

class ManagePurchases extends ManageRelatedRecords
{
    protected static string $resource = VendorResource::class;

    protected static string $relationship = 'orders';

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/vendor/pages/manage-purchases.navigation.title');
    }

    public function table(Table $table): Table
    {
        return QuotationResource::table($table)
            ->modifyQueryUsing(fn ($query) => $query->where('partner_id', $this->record->getKey()))
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => QuotationResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => QuotationResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }
}
