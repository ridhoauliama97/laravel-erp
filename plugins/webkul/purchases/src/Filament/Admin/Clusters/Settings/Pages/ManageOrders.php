<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageOrders extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $slug = 'purchase/manage-orders';

    protected static ?string $navigationGroup = 'Purchase';

    protected static ?int $navigationSort = 1;

    protected static string $settings = OrderSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('purchases::filament/admin/clusters/settings/pages/manage-orders.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('purchases::filament/admin/clusters/settings/pages/manage-orders.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/settings/pages/manage-orders.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Toggle::make('enable_order_approval')
                            ->label(__('purchases::filament/admin/clusters/settings/pages/manage-orders.form.enable-order-approval'))
                            ->helperText(__('purchases::filament/admin/clusters/settings/pages/manage-orders.form.enable-order-approval-helper-text'))
                            ->live(),
                        Forms\Components\TextInput::make('order_validation_amount')
                            ->label(__('purchases::filament/admin/clusters/settings/pages/manage-orders.form.min-amount'))
                            ->inlineLabel()
                            ->numeric()
                            ->default(0)
                            ->visible(fn (Forms\Get $get): bool => $get('enable_order_approval')),
                    ]),
                Forms\Components\Toggle::make('enable_lock_confirmed_orders')
                    ->label(__('purchases::filament/admin/clusters/settings/pages/manage-orders.form.enable-lock-confirmed-orders'))
                    ->helperText(__('purchases::filament/admin/clusters/settings/pages/manage-orders.form.enable-lock-confirmed-orders-helper-text')),
                Forms\Components\Toggle::make('enable_purchase_agreements')
                    ->label(__('purchases::filament/admin/clusters/settings/pages/manage-orders.form.enable-purchase-agreements'))
                    ->helperText(__('purchases::filament/admin/clusters/settings/pages/manage-orders.form.enable-purchase-agreements-helper-text')),
            ]);
    }
}
