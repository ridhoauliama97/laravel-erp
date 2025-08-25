<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Support\Models\ActivityPlan;

class ListActivityPlans extends ListRecords
{
    protected static string $resource = ActivityPlanResource::class;

    protected static ?string $pluginName = 'employees';

    protected static function getPluginName()
    {
        return static::$pluginName;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label(__('employees::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plan.header-actions.create.label'))
                ->mutateFormDataUsing(function ($data) {
                    $user = Auth::user();

                    $data['plugin'] = static::getPluginName();

                    $data['creator_id'] = $user->id;

                    $data['company_id'] ??= $user->defaultCompany?->id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plan.header-actions.create.notification.title'))
                        ->body(__('employees::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plan.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('employees::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plan.tabs.all'))
                ->badge(ActivityPlan::where('plugin', static::getPluginName())->count()),
            'archived' => Tab::make(__('employees::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plan.tabs.archived'))
                ->badge(ActivityPlan::where('plugin', static::getPluginName())->onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('plugin', static::getPluginName())->onlyTrashed();
                }),
        ];
    }
}
