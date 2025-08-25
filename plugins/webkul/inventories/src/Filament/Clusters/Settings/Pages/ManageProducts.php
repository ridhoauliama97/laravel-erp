<?php

namespace Webkul\Inventory\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Route;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageProducts extends SettingsPage
{
    use HasPageShield;

    protected static ?string $slug = 'inventory/manage-products';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

    protected static string $settings = ProductSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('inventories::filament/clusters/settings/pages/manage-products.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('inventories::filament/clusters/settings/pages/manage-products.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/settings/pages/manage-products.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('enable_variants')
                    ->label(__('inventories::filament/clusters/settings/pages/manage-products.form.enable-variants'))
                    ->helperText(__('inventories::filament/clusters/settings/pages/manage-products.form.enable-variants-helper-text')),
                Forms\Components\Toggle::make('enable_uom')
                    ->label(__('inventories::filament/clusters/settings/pages/manage-products.form.enable-uom'))
                    ->helperText(__('inventories::filament/clusters/settings/pages/manage-products.form.enable-uom-helper-text')),
                Forms\Components\Toggle::make('enable_packagings')
                    ->label(__('inventories::filament/clusters/settings/pages/manage-products.form.enable-packagings'))
                    ->helperText(function () {
                        $routeBaseName = PackagingResource::getRouteBaseName();

                        $url = '#';

                        if (Route::has("{$routeBaseName}.index")) {
                            $url = PackagingResource::getUrl();
                        }

                        return new \Illuminate\Support\HtmlString(__('inventories::filament/clusters/settings/pages/manage-products.form.enable-packagings-helper-text').'</br><a href="'.$url.'" class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action"><svg style="--c-400:var(--primary-400);--c-600:var(--primary-600)" class="fi-link-icon h-5 w-5 text-custom-600 dark:text-custom-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path></svg><span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--primary-400);--c-600:var(--primary-600)">'.__('inventories::filament/clusters/settings/pages/manage-products.form.configure-packagings').'</span></a>');
                    }),
            ]);
    }
}
