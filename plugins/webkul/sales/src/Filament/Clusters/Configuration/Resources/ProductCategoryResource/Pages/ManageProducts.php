<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\ManageProducts as BaseManageProducts;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource;

class ManageProducts extends BaseManageProducts
{
    protected static string $resource = ProductCategoryResource::class;
}
