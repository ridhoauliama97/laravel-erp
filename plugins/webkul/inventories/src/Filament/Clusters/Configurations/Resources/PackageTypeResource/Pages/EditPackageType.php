<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource;

class EditPackageType extends EditRecord
{
    protected static string $resource = PackageTypeResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/package-type/pages/edit-package-type.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/package-type/pages/edit-package-type.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/package-type/pages/edit-package-type.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/package-type/pages/edit-package-type.header-actions.delete.notification.body')),
                ),
        ];
    }
}
