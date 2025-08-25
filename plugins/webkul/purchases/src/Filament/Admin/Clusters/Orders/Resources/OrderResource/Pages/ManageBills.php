<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Livewire;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;

class ManageBills extends ManageRelatedRecords
{
    protected static string $resource = OrderResource::class;

    protected static string $relationship = 'accountMoves';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/order/pages/manage-bills.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->accountMoves()->count();
    }

    public function table(Table $table): Table
    {
        return BillResource::table($table)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => BillResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => BillResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }
}
