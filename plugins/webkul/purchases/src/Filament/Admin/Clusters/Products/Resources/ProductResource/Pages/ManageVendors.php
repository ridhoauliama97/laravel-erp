<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Models\ProductSupplier;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ManageVendors extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'supplierInformation';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/products/resources/product/pages/manage-vendors.title');
    }

    public function form(Form $form): Form
    {
        $form = VendorPriceResource::form($form);

        if ($this->getRecord()->is_configurable) {
            $components = $form->getComponents();

            $secondGroupChildComponents = $components[1]->getChildComponents();

            $secondGroupFirstSectionChildComponents = $secondGroupChildComponents[0]->getChildComponents();

            array_unshift($secondGroupFirstSectionChildComponents, Forms\Components\Select::make('product_id')
                ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.product'))
                ->relationship(
                    'product',
                    'name',
                    fn (Builder $query) => $query->where('parent_id', $this->getRecord()->id),
                )
                ->required()
                ->searchable()
                ->preload(),
            );

            $secondGroupChildComponents[0]->childComponents($secondGroupFirstSectionChildComponents);

            $components[1]->childComponents($secondGroupChildComponents);

            $form->components($components);
        }

        return $form;
    }

    public function table(Table $table): Table
    {
        return VendorPriceResource::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('purchases::filament/admin/clusters/products/resources/product/pages/manage-vendors.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['product_id'] ??= $this->getOwnerRecord()->id;

                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->action(function (array $data) {
                        if ($this->getOwnerRecord()->is_configurable) {
                            ProductSupplier::create($data);
                        } else {
                            $data['product_id'] = $this->getOwnerRecord()->id;

                            $this->getOwnerRecord()->supplierInformation()->create($data);
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('purchases::filament/admin/clusters/products/resources/product/pages/manage-vendors.table.header-actions.create.notification.title'))
                            ->body(__('purchases::filament/admin/clusters/products/resources/product/pages/manage-vendors.table.header-actions.create.notification.body')),
                    ),
            ])
            ->emptyStateActions([]);
    }
}
