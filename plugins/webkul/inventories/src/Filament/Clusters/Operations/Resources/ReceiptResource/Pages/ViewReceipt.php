<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Operations\Actions as OperationActions;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;
use Webkul\Inventory\Models\Receipt;

class ViewReceipt extends ViewRecord
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            Actions\ActionGroup::make([
                OperationActions\Print\PickingOperationAction::make(),
                OperationActions\Print\DeliverySlipAction::make(),
                OperationActions\Print\PackageAction::make(),
                OperationActions\Print\LabelsAction::make(),
            ])
                ->label(__('inventories::filament/clusters/operations/resources/receipt/pages/edit-receipt.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->button(),
            Actions\DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == Enums\OperationState::DONE)
                ->action(function (Actions\DeleteAction $action, Receipt $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/operations/resources/receipt/pages/view-receipt.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/receipt/pages/view-receipt.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/operations/resources/receipt/pages/view-receipt.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/operations/resources/receipt/pages/view-receipt.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
