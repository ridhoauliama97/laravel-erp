<?php

namespace Webkul\Support\Filament\Resources\ActivityTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Webkul\Support\Filament\Resources\ActivityTypeResource;
use Webkul\Support\Models\ActivityType;

class ListActivityTypes extends ListRecords
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'employees';

    protected static function getPluginName()
    {
        return static::$pluginName;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('support::filament/resources/activity-type/pages/list-activity-type.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('support::filament/resources/activity-type/pages/list-activity-type.tabs.all'))
                ->badge(ActivityType::where('plugin', static::getPluginName())->count()),
            'archived' => Tab::make(__('support::filament/resources/activity-type/pages/list-activity-type.tabs.archived'))
                ->badge(ActivityType::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('plugin', static::getPluginName())->onlyTrashed();
                }),
        ];
    }
}
