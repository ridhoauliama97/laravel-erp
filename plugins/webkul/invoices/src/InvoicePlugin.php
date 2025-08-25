<?php

namespace Webkul\Invoice;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Webkul\Invoice\Filament\Clusters\Settings\Pages\Products;
use Webkul\Support\Package;

class InvoicePlugin implements Plugin
{
    public function getId(): string
    {
        return 'invoices';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\Invoice\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\Invoice\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\Invoice\\Filament\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\Invoice\\Filament\\Widgets')
                    ->navigationItems([
                        NavigationItem::make('settings')
                            ->label('Settings')
                            ->url(fn () => Products::getUrl())
                            ->icon('heroicon-o-wrench')
                            ->group('Invoices')
                            ->sort(4),
                    ]);
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new \ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()).($path ?? '');
    }
}
