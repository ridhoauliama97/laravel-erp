<?php

namespace Webkul\Chatter\Livewire;

use Carbon\Carbon;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\WithFileUploads;
use Webkul\Chatter\Filament\Actions\Chatter\ActivityAction;
use Webkul\Chatter\Filament\Actions\Chatter\FileAction;
use Webkul\Chatter\Filament\Actions\Chatter\FollowerAction;
use Webkul\Chatter\Filament\Actions\Chatter\LogAction;
use Webkul\Chatter\Filament\Actions\Chatter\MessageAction;
use Webkul\Chatter\Filament\Infolists\Components\Activities\ActivitiesRepeatableEntry;
use Webkul\Chatter\Filament\Infolists\Components\Activities\ContentTextEntry as ActivityContentTextEntry;
use Webkul\Chatter\Filament\Infolists\Components\Activities\TitleTextEntry as ActivityTitleTextEntry;
use Webkul\Chatter\Filament\Infolists\Components\Messages\ContentTextEntry as MessageContentTextEntry;
use Webkul\Chatter\Filament\Infolists\Components\Messages\MessageRepeatableEntry;
use Webkul\Chatter\Filament\Infolists\Components\Messages\TitleTextEntry as MessageTitleTextEntry;
use Webkul\Chatter\Models\Message;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityPlan;
use Webkul\Support\Models\ActivityType;

class ChatterPanel extends Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions, InteractsWithForms, InteractsWithInfolists, WithFileUploads;

    public Model $record;

    public mixed $activityPlans = [];

    public string $resource = '';

    public mixed $followerViewMail = null;

    public mixed $messageViewMail = null;

    public bool $showMessageAction;

    public bool $showActivityAction;

    public bool $showFollowerAction;

    public bool $showLogAction;

    public bool $showFileAction;

    public array $filters;

    public function mount(
        Model $record,
        string $resource = '',
        mixed $activityPlans = [],
        string|Closure|null $followerViewMail = null,
        string|Closure|null $messageViewMail = null,
        bool $showMessageAction = true,
        bool $showActivityAction = true,
        bool $showFollowerAction = true,
        bool $showLogAction = true,
        bool $showFileAction = true,
        array $filters = [],
    ): void {
        $this->record = $record;

        $this->activityPlans = $activityPlans;

        $this->followerViewMail = $followerViewMail;

        $this->messageViewMail = $messageViewMail;

        $this->resource = $resource;

        $this->showMessageAction = $showMessageAction;

        $this->showActivityAction = $showActivityAction;

        $this->showFollowerAction = $showFollowerAction;

        $this->showLogAction = $showLogAction;

        $this->showFileAction = $showFileAction;

        $this->filters = $filters;
    }

    public function messageAction(): MessageAction
    {
        return MessageAction::make('message')
            ->visible($this->showMessageAction)
            ->setMessageMailView($this->messageViewMail)
            ->setResource($this->resource)
            ->record($this->record);
    }

    public function logAction(): LogAction
    {
        return LogAction::make('log')
            ->visible($this->showLogAction)
            ->record($this->record);
    }

    public function fileAction(): FileAction
    {
        return FileAction::make('file')
            ->visible($this->showFileAction)
            ->hiddenLabel()
            ->record($this->record);
    }

    public function followerAction(): FollowerAction
    {
        return FollowerAction::make('follower')
            ->visible($this->showFollowerAction)
            ->setFollowerMailView($this->followerViewMail)
            ->setResource($this->resource)
            ->record($this->record);
    }

    public function activityAction(): ActivityAction
    {
        return ActivityAction::make('activity')
            ->visible($this->showActivityAction)
            ->setActivityPlans($this->activityPlans)
            ->record($this->record);
    }

    public function removeFollower($partnerId)
    {
        $partner = Partner::findOrFail($partnerId);

        $this->record->removeFollower($partner);
    }

    public function markAsDoneAction(): Action
    {
        return Action::make('markAsDone')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->modalIcon('heroicon-o-check-circle')
            ->label(__('chatter::livewire/chatter-panel.mark-as-done.title'))
            ->form(fn (Form $form) => $form->schema([
                TextInput::make('feedback')
                    ->label(__('chatter::livewire/chatter-panel.mark-as-done.form.fields.feedback')),
                Hidden::make('type'),
            ]))
            ->modalFooterActions(fn ($livewire, $arguments): array => [
                Action::make('doneAndScheduleNext')
                    ->icon('heroicon-o-arrow-uturn-right')
                    ->label(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.label'))
                    ->modalIcon('heroicon-o-arrow-uturn-right')
                    ->action(function () use ($livewire, $arguments) {
                        $this->processMessage($arguments['id'], $livewire->mountedActionsData[0]['feedback'] ?? null);

                        $this->replaceMountedAction('activity');

                        Notification::make()
                            ->success()
                            ->title(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.actions.notification.mark-as-done.title'))
                            ->body(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.actions.notification.mark-as-done.body'))
                            ->send();
                    })
                    ->cancelParentActions(),
                Action::make('done')
                    ->icon('heroicon-o-check-circle')
                    ->label('Done')
                    ->modalIcon('heroicon-o-check-circle')
                    ->action(function () use ($livewire, $arguments) {
                        $this->processMessage($arguments['id'], $livewire->mountedActionsData[0]['feedback'] ?? null);

                        Notification::make()
                            ->success()
                            ->title(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.actions.notification.mark-as-done.title'))
                            ->body(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.actions.notification.mark-as-done.body'))
                            ->send();
                    })
                    ->cancelParentActions(),
            ]);
    }

    /**
     * Process the message, add a comment, and delete the message.
     */
    protected function processMessage(int $messageId, ?string $feedback): void
    {
        $message = Message::find($messageId);

        if (! $message) {
            return;
        }

        $this->record->addMessage([
            'type' => 'comment',
            'body' => collect([
                $message->activityType?->name ? $message->activityType?->name.' done' : null,
                $message->summary ? $message->summary : null,
                $message->body ? __('chatter::livewire/chatter-panel.process-message.original-note', ['body' => $message->body]) : null,
                $feedback ? __('chatter::livewire/chatter-panel.process-message.feedback', ['feedback' => $feedback]) : null,
            ])->filter()->implode(''),
        ]);

        $message->delete();
    }

    public function editActivityAction(): Action
    {
        return Action::make('editActivity')
            ->icon('heroicon-o-pencil-square')
            ->modalIcon('heroicon-o-pencil-square')
            ->color('primary')
            ->label(__('chatter::livewire/chatter-panel.edit-activity.title'))
            ->mountUsing(function (Forms\Form $form, $livewire) {
                $activityId = $livewire->mountedActionsArguments[0]['id'];

                $record = Message::find($activityId);

                $form->fill([
                    'activity_plan_id' => $record->activity_plan_id,
                    'activity_type_id' => $record->activity_type_id,
                    'date_deadline'    => $record->date_deadline,
                    'summary'          => $record->summary,
                    'assigned_to'      => $record->assigned_to,
                    'body'             => $record->body,
                    'type'             => $record->type ?? 'activity',
                ]);
            })
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\Select::make('activity_plan_id')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.activity-plan'))
                                        ->options($this->activityPlans)
                                        ->searchable()
                                        ->preload()
                                        ->live(),
                                    Forms\Components\DatePicker::make('date_deadline')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.plan-date'))
                                        ->hidden(fn (Get $get) => ! $get('activity_plan_id'))
                                        ->live()
                                        ->native(false),
                                ])
                                ->columns(2),

                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\Placeholder::make('plan_summary')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.plan-summary'))
                                        ->content(function (Get $get) {
                                            if (! $get('activity_plan_id')) {
                                                return null;
                                            }

                                            $activityPlanTemplates = ActivityPlan::find($get('activity_plan_id'))
                                                ->activityPlanTemplates;

                                            $html = '<div class="space-y-2">';
                                            foreach ($activityPlanTemplates as $activityPlanTemplate) {
                                                $planDate = $get('date_deadline') ? Carbon::parse($get('date_deadline'))->format('m/d/Y') : '';
                                                $html .= '<div class="flex items-center space-x-2" style="margin-left: 20px;">
                                                            <span>•</span>
                                                            <span style="margin-left:2px;">'.$activityPlanTemplate->summary.($planDate ? ' ('.$planDate.')' : '').'</span>
                                                          </div>';
                                            }
                                            $html .= '</div>';

                                            return new HtmlString($html);
                                        })->hidden(fn (Get $get) => ! $get('activity_plan_id')),
                                    Forms\Components\Select::make('activity_type_id')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.activity-type'))
                                        ->options(ActivityType::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->required()
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    Forms\Components\DatePicker::make('date_deadline')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.due-date'))
                                        ->native(false)
                                        ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))?->category == 'meeting' : false)
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    Forms\Components\TextInput::make('summary')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.summary'))
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    Forms\Components\Select::make('assigned_to')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.assigned-to'))
                                        ->searchable()
                                        ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))?->category == 'meeting' : false)
                                        ->live()
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id'))
                                        ->options(User::all()->pluck('name', 'id')->toArray())
                                        ->required(),
                                ])->columns(2),
                            Forms\Components\RichEditor::make('body')
                                ->hiddenLabel()
                                ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))?->category == 'meeting' : false)
                                ->visible(fn (Get $get) => ! $get('activity_plan_id'))
                                ->label(__('chatter::app.filament.actions.chatter.activity.form.type-your-message-here'))
                                ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                            Forms\Components\Hidden::make('type')
                                ->default('activity'),
                        ]),
                ]);
            })
            ->action(function (array $data, $livewire) {
                $activityId = $livewire->mountedActionsArguments[0]['id'];

                $record = Message::find($activityId);

                $record->update($data);

                Notification::make()
                    ->success()
                    ->title('Activity updated successfully')
                    ->title(__('chatter::livewire/chatter-panel.edit-activity.action.notification.success.title'))
                    ->body(__('chatter::livewire/chatter-panel.edit-activity.action.notification.success.body'))
                    ->send();
            });
    }

    public function deleteMessageAction(): Action
    {
        return Action::make('deleteMessage')
            ->requiresConfirmation()
            ->action(fn (array $arguments) => $this->record->removeMessage($arguments['id']));
    }

    public function cancelActivityAction(): Action
    {
        return Action::make('cancelActivity')
            ->icon('heroicon-o-trash')
            ->label(__('chatter::livewire/chatter-panel.cancel-activity-plan-action.title'))
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn (array $arguments) => $this->record->removeMessage($arguments['id'], 'activities'));
    }

    public function chatInfolist(Infolist $infolist): Infolist
    {
        $record = $this->record;

        $state = $record->withFilters($this->filters);

        return $infolist
            ->record($record)
            ->schema([
                MessageRepeatableEntry::make('messages')
                    ->state($state)
                    ->hiddenLabel()
                    ->schema([
                        MessageTitleTextEntry::make('user')
                            ->hiddenLabel(),
                        MessageContentTextEntry::make('content')
                            ->hiddenLabel(),
                    ])
                    ->placeholder(__('chatter::livewire/chatter-panel.placeholders.no-record-found')),
            ]);
    }

    public function activityInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema(function () {

                if ($this->record->activities->isEmpty()) {
                    return [];
                }

                return [
                    Section::make(__('chatter::livewire/chatter-panel.activity-infolist.title'))
                        ->collapsible()
                        ->compact()
                        ->schema([
                            ActivitiesRepeatableEntry::make('activities')
                                ->hiddenLabel()
                                ->schema([
                                    ActivityTitleTextEntry::make('user')
                                        ->hiddenLabel(),
                                    ActivityContentTextEntry::make('content')
                                        ->hiddenLabel(),
                                ])
                                ->placeholder(__('chatter::livewire/chatter-panel.placeholders.no-record-found')),
                        ]),
                ];
            });
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="flex items-center justify-center w-full">
                <div class="flex flex-col items-center space-y-4">
                    <x-filament::loading-indicator class="w-10 h-10 text-primary-500 animate-spin" />
                    <p class="text-sm font-medium tracking-wide text-gray-600 dark:text-gray-300">
                        {{ __('chatter::livewire/chatter-panel.placeholders.loading') }}
                    </p>
                </div>
            </div>
        HTML;
    }

    public function render(): View
    {
        return view('chatter::livewire.chatter-panel');
    }
}
