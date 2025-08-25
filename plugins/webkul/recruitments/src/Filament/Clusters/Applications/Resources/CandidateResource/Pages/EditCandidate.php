<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;
use Webkul\Recruitment\Models\Candidate;

class EditCandidate extends EditRecord
{
    protected static string $resource = CandidateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('recruitments::filament/clusters/applications/resources/candidate/pages/edit-candidate.notification.title'))
            ->body(__('recruitments::filament/clusters/applications/resources/candidate/pages/edit-candidate.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('gotoEmployee')
                ->tooltip(__('recruitments::filament/clusters/applications/resources/candidate/pages/edit-candidate.goto-employee-tooltip'))
                ->visible(fn ($record) => $record->employee_id)
                ->icon('heroicon-s-arrow-top-right-on-square')
                ->iconButton()
                ->action(function (Candidate $record) {
                    $employee = $record->createEmployee();

                    return redirect(EmployeeResource::getUrl('view', ['record' => $employee]));
                }),
            Action::make('createEmployee')
                ->label(__('recruitments::filament/clusters/applications/resources/candidate/pages/edit-candidate.create-employee'))
                ->hidden(fn ($record) => $record->employee_id)
                ->action(function (Candidate $record) {
                    $employee = $record->createEmployee();

                    return redirect(EmployeeResource::getUrl('edit', ['record' => $employee]));
                }),
            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/applications/resources/candidate/pages/edit-candidate.header-actions.delete.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/candidate/pages/edit-candidate.header-actions.delete.notification.body'))
                ),
        ];
    }
}
