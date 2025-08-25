<?php

namespace Webkul\Website\Filament\Admin\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Website\Filament\Admin\Resources\PageResource;
use Webkul\Website\Models\Page;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('website::filament/admin/resources/page/pages/edit-record.notification.title'))
            ->body(__('website::filament/admin/resources/page/pages/edit-record.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publish')
                ->label(__('website::filament/admin/resources/page/pages/edit-record.header-actions.publish.label'))
                ->icon('heroicon-o-check-circle')
                ->action(function (Page $record) {
                    $record->update([
                        'published_at' => now(),
                        'is_published' => true,
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('website::filament/admin/resources/page/pages/edit-record.header-actions.publish.notification.title'))
                        ->body(__('website::filament/admin/resources/page/pages/edit-record.header-actions.publish.notification.body'))
                        ->send();
                })
                ->visible(fn (Page $record) => ! $record->is_published),
            Actions\Action::make('draft')
                ->label(__('website::filament/admin/resources/page/pages/edit-record.header-actions.draft.label'))
                ->icon('heroicon-o-archive-box')
                ->action(function (Page $record) {
                    $record->update(['is_published' => false]);

                    Notification::make()
                        ->success()
                        ->title(__('website::filament/admin/resources/page/pages/edit-record.header-actions.draft.notification.title'))
                        ->body(__('website::filament/admin/resources/page/pages/edit-record.header-actions.draft.notification.body'))
                        ->send();
                })
                ->visible(fn (Page $record) => $record->is_published),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('website::filament/admin/resources/page/pages/edit-record.header-actions.delete.notification.title'))
                        ->body(__('website::filament/admin/resources/page/pages/edit-record.header-actions.delete.notification.body')),
                ),
        ];
    }
}
