<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\CreateProductAttribute as BaseCreateProductAttribute;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource;

class CreateProductAttribute extends BaseCreateProductAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
