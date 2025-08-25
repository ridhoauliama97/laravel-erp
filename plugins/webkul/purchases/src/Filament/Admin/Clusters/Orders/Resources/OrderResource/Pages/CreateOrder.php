<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Enums;
use Webkul\Purchase\Facades\PurchaseOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('purchases::filament/admin/clusters/orders/resources/order/pages/create-order.notification.title'))
            ->body(__('purchases::filament/admin/clusters/orders/resources/order/pages/create-order.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        $data['calendar_start_at'] = $data['ordered_at'];

        $data['state'] ??= Enums\OrderState::DRAFT;

        return $data;
    }

    protected function afterCreate(): void
    {
        PurchaseOrder::computePurchaseOrder($this->getRecord());
    }
}
