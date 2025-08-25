<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource;

class EditOperationType extends EditRecord
{
    protected static string $resource = OperationTypeResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/operation-type/pages/edit-operation-type.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/operation-type/pages/edit-operation-type.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/operation-type/pages/edit-operation-type.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/operation-type/pages/edit-operation-type.header-actions.delete.notification.body')),
                ),
        ];
    }
}
