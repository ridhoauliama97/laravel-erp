<?php

namespace Webkul\Inventory\Filament\Clusters;

use Filament\Clusters\Cluster;

class Products extends Cluster
{
    protected static ?string $slug = 'inventory/products';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/products.navigation.group');
    }
}
