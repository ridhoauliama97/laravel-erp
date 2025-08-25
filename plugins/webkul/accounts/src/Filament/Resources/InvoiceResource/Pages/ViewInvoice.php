<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions as ChatterActions;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

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
            BaseActions\PreviewAction::make()
                ->setTemplate('accounts::invoice/actions/preview.index'),
            BaseActions\PrintAndSendAction::make(),
            BaseActions\CreditNoteAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.body'))
                ),
        ];
    }
}
