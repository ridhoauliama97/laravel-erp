<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\ProductQuantity;

class ValidateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.validate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('inventories::filament/clusters/operations/actions/validate.label'))
            ->color(function ($record) {
                if (in_array($record->state, [Enums\OperationState::DRAFT, Enums\OperationState::CONFIRMED])) {
                    return 'gray';
                }

                return 'primary';
            })
            ->requiresConfirmation(function (Operation $record) {
                return $record->operationType->create_backorder === Enums\CreateBackorder::ASK
                    && Inventory::canCreateBackOrder($record);
            })
            ->configureModal($this->getRecord())
            ->action(function (Operation $record, Component $livewire): void {
                if ($this->hasMoveErrors($record)) {
                    return;
                }

                Inventory::createBackOrder($record);

                Inventory::validateTransfer($record);

                $livewire->updateForm();
            })
            ->hidden(fn () => in_array($this->getRecord()->state, [
                Enums\OperationState::DONE,
                Enums\OperationState::CANCELED,
            ]));
    }

    protected function configureModal(Operation $record): self
    {
        if (
            $record->operationType->create_backorder === Enums\CreateBackorder::ASK
            && Inventory::canCreateBackOrder($record)
        ) {
            $this->modalHeading(__('inventories::filament/clusters/operations/actions/validate.modal-heading'))
                ->modalDescription(__('inventories::filament/clusters/operations/actions/validate.modal-description'))
                ->extraModalFooterActions([
                    Action::make('no-backorder')
                        ->label(__('inventories::filament/clusters/operations/actions/validate.extra-modal-footer-actions.no-backorder.label'))
                        ->color('danger')
                        ->action(function (Operation $record, Component $livewire): void {
                            if ($this->hasMoveErrors($record)) {
                                return;
                            }

                            Inventory::validateTransfer($record);

                            $livewire->updateForm();
                        }),
                ]);
        }

        return $this;
    }

    protected function hasMoveErrors(Operation $record): bool
    {
        $record = Inventory::computeTransfer($record);

        // Validate moves and notify on warnings.
        foreach ($record->moves as $move) {
            if ($this->hasMoveLineErrors($move)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the move lines are valid.
     *
     * @return bool Returns false if a validation warning is triggered.
     */
    private function hasMoveLineErrors($move): bool
    {
        if ($move->lines->isEmpty()) {
            $this->sendNotification(
                'inventories::filament/clusters/operations/actions/validate.notification.warning.lines-missing.title',
                'inventories::filament/clusters/operations/actions/validate.notification.warning.lines-missing.body',
                'warning'
            );

            return true;
        }

        foreach ($move->lines as $line) {
            if ($line->package_id && $line->result_package_id && $line->package_id == $line->result_package_id) {
                $sourceQuantity = ProductQuantity::where('product_id', $line->product_id)
                    ->where('location_id', $line->source_location_id)
                    ->where('lot_id', $line->lot_id)
                    ->where('package_id', $line->package_id)
                    ->first();

                if ($sourceQuantity && $sourceQuantity->quantity != $line->qty) {
                    $this->sendNotification(
                        'inventories::filament/clusters/operations/actions/validate.notification.warning.partial-package.title',
                        'inventories::filament/clusters/operations/actions/validate.notification.warning.partial-package.body',
                        'warning'
                    );

                    return true;
                }
            }
        }

        $isLotTracking = $move->product->tracking == Enums\ProductTracking::LOT || $move->product->tracking == Enums\ProductTracking::SERIAL;

        if ($isLotTracking && $move->lines->contains(fn ($line) => ! $line->lot_id)) {
            $this->sendNotification(
                'inventories::filament/clusters/operations/actions/validate.notification.warning.lot-missing.title',
                'inventories::filament/clusters/operations/actions/validate.notification.warning.lot-missing.body',
                'warning'
            );

            return true;
        }

        $isSerialTracking = $move->product->tracking == Enums\ProductTracking::SERIAL;

        if ($isSerialTracking) {
            if ($move->lines->contains(fn ($line) => $line->qty != 1)) {
                $this->sendNotification(
                    'inventories::filament/clusters/operations/actions/validate.notification.warning.serial-qty.title',
                    'inventories::filament/clusters/operations/actions/validate.notification.warning.serial-qty.body',
                    'warning'
                );

                return true;
            }

            $lots = $move->lines->pluck('lot_id');

            if ($lots->count() !== $lots->unique()->count()) {
                $this->sendNotification(
                    'inventories::filament/clusters/operations/actions/validate.notification.warning.serial-qty.title',
                    'inventories::filament/clusters/operations/actions/validate.notification.warning.serial-qty.body',
                    'warning'
                );

                return true;
            }
        }

        return false;
    }

    /**
     * Send a notification with the given title, body and type.
     */
    private function sendNotification(string $titleKey, string $bodyKey, string $type = 'info'): void
    {
        Notification::make()
            ->title(__($titleKey))
            ->body(__($bodyKey))
            ->{$type}()
            ->send();
    }
}
