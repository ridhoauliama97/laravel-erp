<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Livewire;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Support\Package;

class ManageReceipts extends ManageRelatedRecords
{
    protected static string $resource = OrderResource::class;

    protected static string $relationship = 'operations';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
            return false;
        }

        return Package::isPluginInstalled('inventories');
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/order/pages/manage-receipts.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->operations()->count();
    }

    public function table(Table $table): Table
    {
        return OperationResource::table($table)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => OperationResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => OperationResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ])
            ->bulkActions([]);
    }
}
