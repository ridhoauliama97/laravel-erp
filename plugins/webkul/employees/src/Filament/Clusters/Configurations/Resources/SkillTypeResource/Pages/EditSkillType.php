<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource;

class EditSkillType extends EditRecord
{
    protected static string $resource = SkillTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('employees::filament/clusters/configurations/resources/skill-type/pages/edit-skill-type.notification.title'))
            ->body(__('employees::filament/clusters/configurations/resources/skill-type/pages/edit-skill-type.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/clusters/configurations/resources/skill-type/pages/edit-skill-type.header-actions.delete.notification.title'))
                        ->body(__('employees::filament/clusters/configurations/resources/skill-type/pages/edit-skill-type.header-actions.delete.notification.body')),
                ),
        ];
    }
}
