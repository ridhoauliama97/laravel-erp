<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Models\Location;

class ViewLocation extends ViewRecord
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('inventories::filament/clusters/configurations/resources/location/pages/view-location.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function (Location $record) {
                    $pdf = PDF::loadView('inventories::filament.clusters.configurations.locations.actions.print', [
                        'records' => collect([$record]),
                    ]);

                    $pdf->setPaper('a4', 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Location-'.$record->name.'.pdf');
                }),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/location/pages/view-location.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/location/pages/view-location.header-actions.delete.notification.body')),
                ),
        ];
    }
}
