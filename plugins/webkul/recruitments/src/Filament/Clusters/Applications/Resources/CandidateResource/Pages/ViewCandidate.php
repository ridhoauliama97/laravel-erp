<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;
use Webkul\Recruitment\Models\Candidate;

class ViewCandidate extends ViewRecord
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('gotoEmployee')
                ->tooltip(__('recruitments::filament/clusters/applications/resources/candidate/pages/view-candidate.goto-employee-tooltip'))
                ->visible(fn ($record) => $record->employee_id)
                ->icon('heroicon-s-arrow-top-right-on-square')
                ->iconButton()
                ->action(function (Candidate $record) {
                    $employee = $record->createEmployee();

                    return redirect(EmployeeResource::getUrl('view', ['record' => $employee]));
                }),
            Action::make('createEmployee')
                ->label(__('recruitments::filament/clusters/applications/resources/candidate/pages/view-candidate.create-employee'))
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
                        ->title(__('recruitments::filament/clusters/applications/resources/candidate/pages/view-candidate.header-actions.delete.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/candidate/pages/view-candidate.header-actions.delete.notification.body'))
                ),
        ];
    }
}
