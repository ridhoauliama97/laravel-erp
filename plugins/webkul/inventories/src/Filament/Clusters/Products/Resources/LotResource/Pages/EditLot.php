<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Models\Lot;

class EditLot extends EditRecord
{
    protected static string $resource = LotResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/products/resources/lot/pages/edit-lot.notification.title'))
            ->body(__('inventories::filament/clusters/products/resources/lot/pages/edit-lot.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('inventories::filament/clusters/products/resources/lot/pages/edit-lot.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function (Lot $record) {
                    $pdf = PDF::loadView('inventories::filament.clusters.products.lots.actions.print', [
                        'records' => collect([$record]),
                    ]);

                    $pdf->setPaper('a4', 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Lot-'.str_replace('/', '_', $record->name).'.pdf');
                }),
            Actions\DeleteAction::make()
                ->action(function (Actions\DeleteAction $action, Lot $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/products/resources/lot/pages/edit-lot.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/products/resources/lot/pages/edit-lot.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/products/resources/lot/pages/edit-lot.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/products/resources/lot/pages/edit-lot.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
