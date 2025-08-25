<?php

namespace Webkul\Project\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use Webkul\Project\Filament\Resources\TaskResource\Pages;
use Webkul\Project\Filament\Resources\TaskResource\RelationManagers;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Project\Settings\TimeSettings;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Support\Filament\Tables\Columns\ProgressBarEntry;

class TaskResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Task::class;

    protected static ?string $slug = 'project/tasks';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/task.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('projects::filament/resources/task.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'project.name', 'partner.name', 'milestone.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('projects::filament/resources/task.global-search.project')   => $record->project?->name ?? '—',
            __('projects::filament/resources/task.global-search.customer')  => $record->partner?->name ?? '—',
            __('projects::filament/resources/task.global-search.milestone') => $record->milestone?->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        ProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->required()
                            ->options(fn () => TaskStage::orderBy('sort')->get()->mapWithKeys(fn ($stage) => [$stage->id => $stage->name]))
                            ->default(TaskStage::first()?->id),
                        Forms\Components\Section::make(__('projects::filament/resources/task.form.sections.general.title'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('projects::filament/resources/task.form.sections.general.fields.title-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                Forms\Components\ToggleButtons::make('state')
                                    ->required()
                                    ->default(TaskState::IN_PROGRESS)
                                    ->inline()
                                    ->options(TaskState::options())
                                    ->colors(TaskState::colors())
                                    ->icons(TaskState::icons()),
                                Forms\Components\Select::make('tags')
                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.name'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique('projects_tags'),
                                                Forms\Components\ColorPicker::make('color')
                                                    ->default('#808080')
                                                    ->hexColor()
                                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.color')),
                                            ])->columns(2),
                                    ]),
                                Forms\Components\RichEditor::make('description')
                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.description')),
                            ]),

                        Forms\Components\Section::make(__('projects::filament/resources/task.form.sections.additional.title'))
                            ->visible(! empty($customFormFields = static::getCustomFormFields()))
                            ->schema($customFormFields),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('projects::filament/resources/task.form.sections.settings.title'))
                            ->schema([
                                Forms\Components\Select::make('project_id')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.project'))
                                    ->relationship('project', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->createOptionForm(fn (Form $form): Form => ProjectResource::form($form))
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('milestone_id', null);
                                    }),
                                Forms\Components\Select::make('milestone_id')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.milestone'))
                                    ->relationship(
                                        name: 'milestone',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query->where('project_id', $get('project_id')),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('projects::filament/resources/task.form.sections.settings.fields.milestone-hint-text'))
                                    ->createOptionForm(fn ($get) => [
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('projects::filament/resources/task.form.sections.settings.fields.name'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\DateTimePicker::make('deadline')
                                            ->label(__('projects::filament/resources/task.form.sections.settings.fields.deadline'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-clock'),
                                        Forms\Components\Toggle::make('is_completed')
                                            ->label(__('projects::filament/resources/task.form.sections.settings.fields.is-completed'))
                                            ->required(),
                                        Forms\Components\Hidden::make('project_id')
                                            ->default($get('project_id')),
                                        Forms\Components\Hidden::make('creator_id')
                                            ->default(fn () => Auth::user()->id),
                                    ])
                                    ->hidden(function (TaskSettings $taskSettings, Forms\Get $get) {
                                        $project = Project::find($get('project_id'));

                                        if (! $project) {
                                            return true;
                                        }

                                        if (! $taskSettings->enable_milestones) {
                                            return true;
                                        }

                                        return ! $project->allow_milestones;
                                    })
                                    ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),
                                Forms\Components\Select::make('partner_id')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.customer'))
                                    ->relationship('partner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Form $form): Form => PartnerResource::form($form))
                                    ->editOptionForm(fn (Form $form): Form => PartnerResource::form($form)),
                                Forms\Components\Select::make('users')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.assignees'))
                                    ->relationship('users', 'name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->createOptionForm(fn (Form $form) => UserResource::form($form)),
                                Forms\Components\DateTimePicker::make('deadline')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.deadline'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar'),
                                Forms\Components\TextInput::make('allocated_hours')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.allocated-hours'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->suffixIcon('heroicon-o-clock')
                                    ->helperText(__('projects::filament/resources/task.form.sections.settings.fields.allocated-hours-helper-text'))
                                    ->dehydrateStateUsing(fn ($state) => $state ?: 0)
                                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                            ]),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        $isTimesheetEnabled = app(TimeSettings::class)->enable_timesheets;

        return $table
            ->columns(static::mergeCustomTableColumns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('projects::filament/resources/task.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('priority')
                    ->label(__('projects::filament/resources/task.table.columns.priority'))
                    ->icon(fn (Task $record): string => $record->priority ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Task $record): string => $record->priority ? 'warning' : 'gray')
                    ->action(function (Task $record): void {
                        $record->update([
                            'priority' => ! $record->priority,
                        ]);
                    }),
                Tables\Columns\IconColumn::make('state')
                    ->label(__('projects::filament/resources/task.table.columns.state'))
                    ->sortable()
                    ->toggleable()
                    ->icon(fn (string $state): string => TaskState::icons()[$state])
                    ->color(fn (string $state): string => TaskState::colors()[$state])
                    ->tooltip(fn (string $state): string => TaskState::options()[$state])
                    ->action(
                        Tables\Actions\Action::make('updateState')
                            ->modalHeading('Update Task State')
                            ->form(fn (Task $record): array => [
                                Forms\Components\ToggleButtons::make('state')
                                    ->label(__('projects::filament/resources/task.table.columns.new-state'))
                                    ->required()
                                    ->default($record->state)
                                    ->inline()
                                    ->options(TaskState::options())
                                    ->colors(TaskState::colors())
                                    ->icons(TaskState::icons()),
                            ])
                            ->modalSubmitActionLabel(__('projects::filament/resources/task.table.columns.update-state'))
                            ->action(function (Task $record, array $data): void {
                                $record->update([
                                    'state' => $data['state'],
                                ]);
                            })
                    ),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('projects::filament/resources/task.table.columns.title'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('projects::filament/resources/task.table.columns.project'))
                    ->hiddenOn(ManageTasks::class)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('projects::filament/resources/task.table.columns.project-placeholder')),
                Tables\Columns\TextColumn::make('milestone.name')
                    ->label(__('projects::filament/resources/task.table.columns.milestone'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),
                Tables\Columns\TextColumn::make('partner.name')
                    ->label(__('projects::filament/resources/task.table.columns.customer'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('users.name')
                    ->label(__('projects::filament/resources/task.table.columns.assignees'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('allocated_hours')
                    ->label(__('projects::filament/resources/task.table.columns.allocated-time'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.allocated-time'))
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                Tables\Columns\TextColumn::make('total_hours_spent')
                    ->label(__('projects::filament/resources/task.table.columns.time-spent'))
                    ->sortable()
                    ->toggleable()
                    ->numeric()
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.time-spent'))
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                Tables\Columns\TextColumn::make('remaining_hours')
                    ->label(__('projects::filament/resources/task.table.columns.time-remaining'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.time-remaining'))
                            ->numeric()
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                ProgressBarEntry::make('progress')
                    ->label(__('projects::filament/resources/task.table.columns.progress'))
                    ->sortable()
                    ->toggleable()
                    ->color(fn (Task $record): string => $record->progress > 100 ? 'danger' : ($record->progress < 100 ? 'warning' : 'success'))
                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                Tables\Columns\TextColumn::make('deadline')
                    ->label(__('projects::filament/resources/task.table.columns.deadline'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tags.name')
                    ->label(__('projects::filament/resources/task.table.columns.tags'))
                    ->badge()
                    ->state(function (Task $record): array {
                        return $record->tags()->get()->map(fn ($tag) => [
                            'label' => $tag->name,
                            'color' => $tag->color ?? '#808080',
                        ])->toArray();
                    })
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state['label'])
                    ->color(fn ($state) => Color::hex($state['color']))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stage.name')
                    ->label(__('projects::filament/resources/task.table.columns.stage'))
                    ->sortable()
                    ->toggleable(),
            ]))
            ->groups([
                Tables\Grouping\Group::make('state')
                    ->label(__('projects::filament/resources/task.table.groups.state'))
                    ->getTitleFromRecordUsing(fn (Task $record): string => TaskState::options()[$record->state]),
                Tables\Grouping\Group::make('project.name')
                    ->label(__('projects::filament/resources/task.table.groups.project')),
                Tables\Grouping\Group::make('deadline')
                    ->label(__('projects::filament/resources/task.table.groups.deadline'))
                    ->date(),
                Tables\Grouping\Group::make('stage.name')
                    ->label(__('projects::filament/resources/task.table.groups.stage')),
                Tables\Grouping\Group::make('milestone.name')
                    ->label(__('projects::filament/resources/task.table.groups.milestone')),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('projects::filament/resources/task.table.groups.customer')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('projects::filament/resources/task.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraints(collect(static::mergeCustomTableQueryBuilderConstraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('title')
                            ->label(__('projects::filament/resources/task.table.filters.title')),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('priority')
                            ->label(__('projects::filament/resources/task.table.filters.priority'))
                            ->options([
                                0 => __('projects::filament/resources/task.table.filters.low'),
                                1 => __('projects::filament/resources/task.table.filters.high'),
                            ])
                            ->icon('heroicon-o-star'),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('state')
                            ->label(__('projects::filament/resources/task.table.filters.state'))
                            ->multiple()
                            ->options(TaskState::options())
                            ->icon('heroicon-o-bars-2'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('tags')
                            ->label(__('projects::filament/resources/task.table.filters.tags'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-tag'),
                        $isTimesheetEnabled
                            ? Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('allocated_hours')
                                ->label(__('projects::filament/resources/task.table.filters.allocated-hours'))
                                ->icon('heroicon-o-clock')
                            : null,
                        $isTimesheetEnabled
                            ? Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('total_hours_spent')
                                ->label(__('projects::filament/resources/task.table.filters.total-hours-spent'))
                                ->icon('heroicon-o-clock')
                            : null,
                        $isTimesheetEnabled
                            ? Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('remaining_hours')
                                ->label(__('projects::filament/resources/task.table.filters.remaining-hours'))
                                ->icon('heroicon-o-clock')
                            : null,
                        $isTimesheetEnabled
                            ? Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('overtime')
                                ->label(__('projects::filament/resources/task.table.filters.overtime'))
                                ->icon('heroicon-o-clock')
                            : null,
                        $isTimesheetEnabled
                            ? Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('progress')
                                ->label(__('projects::filament/resources/task.table.filters.progress'))
                                ->icon('heroicon-o-bars-2')
                            : null,
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('deadline')
                            ->label(__('projects::filament/resources/task.table.filters.deadline'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('projects::filament/resources/task.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('projects::filament/resources/task.table.filters.updated-at')),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('users')
                            ->label(__('projects::filament/resources/task.table.filters.assignees'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-users'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partner')
                            ->label(__('projects::filament/resources/task.table.filters.customer'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('project')
                            ->label(__('projects::filament/resources/task.table.filters.project'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-folder'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('stage')
                            ->label(__('projects::filament/resources/task.table.filters.stage'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-bars-2'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('milestone')
                            ->label(__('projects::filament/resources/task.table.filters.milestone'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-flag'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
                            ->label(__('projects::filament/resources/task.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('creator')
                            ->label(__('projects::filament/resources/task.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ]))->filter()->values()->all()),
            ], layout: \Filament\Tables\Enums\FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) || static::can('forceDelete', $record) || static::can('restore', $record),
            );
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('projects::filament/resources/task.infolist.sections.general.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.title'))
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                                Infolists\Components\TextEntry::make('state')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.state'))
                                    ->badge()
                                    ->icon(fn (string $state): string => TaskState::icons()[$state])
                                    ->color(fn (string $state): string => TaskState::colors()[$state])
                                    ->formatStateUsing(fn (string $state): string => TaskState::options()[$state]),

                                Infolists\Components\IconEntry::make('priority')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.priority'))
                                    ->icon(fn ($record): string => $record->priority ? 'heroicon-s-star' : 'heroicon-o-star')
                                    ->color(fn ($record): string => $record->priority ? 'warning' : 'gray'),

                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.description'))
                                    ->html(),

                                Infolists\Components\TextEntry::make('tags.name')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.tags'))
                                    ->badge()
                                    ->state(function (Task $record): array {
                                        return $record->tags()->get()->map(fn ($tag) => [
                                            'label' => $tag->name,
                                            'color' => $tag->color ?? '#808080',
                                        ])->toArray();
                                    })
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state['label'])
                                    ->color(fn ($state) => Color::hex($state['color']))
                                    ->listWithLineBreaks()
                                    ->separator(', '),
                            ]),

                        Infolists\Components\Section::make(__('projects::filament/resources/task.infolist.sections.project-information.title'))
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('project.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.project'))
                                            ->icon('heroicon-o-folder')
                                            ->placeholder('—')
                                            ->color('primary')
                                            ->url(fn (Task $record): string => $record->project_id ? ProjectResource::getUrl('view', ['record' => $record->project]) : '#'),

                                        Infolists\Components\TextEntry::make('milestone.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.milestone'))
                                            ->icon('heroicon-o-flag')
                                            ->placeholder('—')
                                            ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),

                                        Infolists\Components\TextEntry::make('stage.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.stage'))
                                            ->icon('heroicon-o-queue-list')
                                            ->badge(),

                                        Infolists\Components\TextEntry::make('partner.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.customer'))
                                            ->icon('heroicon-o-queue-list')
                                            ->icon('heroicon-o-phone')
                                            ->listWithLineBreaks()
                                            ->placeholder('—'),

                                        Infolists\Components\TextEntry::make('users.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.assignees'))
                                            ->icon('heroicon-o-users')
                                            ->listWithLineBreaks()
                                            ->placeholder('—'),

                                        Infolists\Components\TextEntry::make('deadline')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.deadline'))
                                            ->icon('heroicon-o-calendar')
                                            ->dateTime()
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        Infolists\Components\Section::make(__('projects::filament/resources/task.infolist.sections.time-tracking.title'))
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('allocated_hours')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.allocated-time'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(' Hours')
                                            ->placeholder('—')
                                            ->formatStateUsing(function ($state) {
                                                $hours = floor($state);
                                                $minutes = ($state - $hours) * 60;

                                                return $hours.':'.$minutes;
                                            })
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                        Infolists\Components\TextEntry::make('total_hours_spent')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-spent'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-spent-suffix'))
                                            ->formatStateUsing(function ($state) {
                                                $hours = floor($state);
                                                $minutes = ($state - $hours) * 60;

                                                return $hours.':'.$minutes;
                                            })
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                        Infolists\Components\TextEntry::make('remaining_hours')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-remaining'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-remaining-suffix'))
                                            ->formatStateUsing(function ($state) {
                                                $hours = floor($state);
                                                $minutes = ($state - $hours) * 60;

                                                return $hours.':'.$minutes;
                                            })
                                            ->color(fn ($state): string => $state < 0 ? 'danger' : 'success')
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                        Infolists\Components\TextEntry::make('progress')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.progress'))
                                            ->icon('heroicon-o-chart-bar')
                                            ->suffix('%')
                                            ->color(
                                                fn ($record): string => $record->progress > 100
                                                    ? 'danger'
                                                    : ($record->progress < 100 ? 'warning' : 'success')
                                            )
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                                    ]),
                            ])
                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                        Infolists\Components\Section::make(__('projects::filament/resources/task.infolist.sections.additional-information.title'))
                            ->visible(! empty($customInfolistEntries = static::getCustomInfolistEntries()))
                            ->schema($customInfolistEntries),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('projects::filament/resources/task.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('projects::filament/resources/task.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('projects::filament/resources/task.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('projects::filament/resources/task.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),

                        Infolists\Components\Section::make(__('projects::filament/resources/task.infolist.sections.statistics.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('subtasks_count')
                                    ->label(__('projects::filament/resources/task.infolist.sections.statistics.entries.sub-tasks'))
                                    ->state(fn (Task $record): int => $record->subTasks()->count())
                                    ->icon('heroicon-o-clipboard-document-list'),

                                Infolists\Components\TextEntry::make('timesheets_count')
                                    ->label(__('projects::filament/resources/task.infolist.sections.statistics.entries.timesheet-entries'))
                                    ->state(fn (Task $record): int => $record->timesheets()->count())
                                    ->icon('heroicon-o-clock')
                                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTask::class,
            Pages\EditTask::class,
            Pages\ManageTimesheets::class,
            Pages\ManageSubTasks::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Timesheets', [
                RelationManagers\TimesheetsRelationManager::class,
            ])
                ->icon('heroicon-o-clock'),

            RelationGroup::make('Sub Tasks', [
                RelationManagers\SubTasksRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListTasks::route('/'),
            'create'     => Pages\CreateTask::route('/create'),
            'edit'       => Pages\EditTask::route('/{record}/edit'),
            'view'       => Pages\ViewTask::route('/{record}'),
            'timesheets' => Pages\ManageTimesheets::route('/{record}/timesheets'),
            'sub-tasks'  => Pages\ManageSubTasks::route('/{record}/sub-tasks'),
        ];
    }
}
