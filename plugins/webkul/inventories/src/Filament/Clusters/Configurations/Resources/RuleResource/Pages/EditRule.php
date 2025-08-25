<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource;

class EditRule extends EditRecord
{
    protected static string $resource = RuleResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/rule/pages/edit-rule.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/rule/pages/edit-rule.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/rule/pages/edit-rule.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/rule/pages/edit-rule.header-actions.delete.notification.body')),
                ),
        ];
    }
}
