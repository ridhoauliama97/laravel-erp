<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Pages;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Settings\TaskSettings;

class ManageMilestones extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'milestones';

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
            return false;
        }

        if (! app(TaskSettings::class)->enable_milestones) {
            return false;
        }

        return $parameters['record']?->allow_milestones;
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/project/pages/manage-milestones.title');
    }

    public function form(Form $form): Form
    {
        return MilestoneResource::form($form);
    }

    public function table(Table $table): Table
    {
        return MilestoneResource::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('projects::filament/resources/project/pages/manage-milestones.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/project/pages/manage-milestones.table.header-actions.create.notification.title'))
                            ->body(__('projects::filament/resources/project/pages/manage-milestones.table.header-actions.create.notification.body')),
                    ),
            ]);
    }
}
