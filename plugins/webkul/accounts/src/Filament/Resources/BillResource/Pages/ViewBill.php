<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\BillResource\Actions\CreditNoteAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions as ChatterActions;

class ViewBill extends ViewRecord
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource($this->getResource()),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            CreditNoteAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/bill/pages/view-bill.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/bill/pages/view-bill.header-actions.delete.notification.body'))
                ),
        ];
    }
}
