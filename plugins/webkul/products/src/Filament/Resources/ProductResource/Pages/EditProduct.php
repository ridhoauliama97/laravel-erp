<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Product\Filament\Resources\ProductResource;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('products::filament/resources/product/pages/edit-product.notification.title'))
            ->body(__('products::filament/resources/product/pages/edit-product.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            Actions\Action::make('print')
                ->label(__('products::filament/resources/product/pages/edit-product.header-actions.print.label'))
                ->color('gray')
                ->icon('heroicon-o-printer')
                ->form([
                    Forms\Components\TextInput::make('quantity')
                        ->label(__('products::filament/resources/product/pages/edit-product.header-actions.print.form.fields.quantity'))
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100),
                    Forms\Components\Radio::make('format')
                        ->label(__('products::filament/resources/product/pages/edit-product.header-actions.print.form.fields.format'))
                        ->options([
                            'dymo'       => __('products::filament/resources/product/pages/edit-product.header-actions.print.form.fields.format-options.dymo'),
                            '2x7_price'  => __('products::filament/resources/product/pages/edit-product.header-actions.print.form.fields.format-options.2x7_price'),
                            '4x7_price'  => __('products::filament/resources/product/pages/edit-product.header-actions.print.form.fields.format-options.4x7_price'),
                            '4x12'       => __('products::filament/resources/product/pages/edit-product.header-actions.print.form.fields.format-options.4x12'),
                            '4x12_price' => __('products::filament/resources/product/pages/edit-product.header-actions.print.form.fields.format-options.4x12_price'),
                        ])
                        ->default('2x7_price')
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    $pdf = PDF::loadView('products::filament.resources.products.actions.print', [
                        'records'  => collect([$record]),
                        'quantity' => $data['quantity'],
                        'format'   => $data['format'],
                    ]);

                    $paperSize = match ($data['format']) {
                        'dymo'  => [0, 0, 252.2, 144],
                        default => 'a4',
                    };

                    $pdf->setPaper($paperSize, 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Product-'.$record->name.'.pdf');
                }),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/product/pages/edit-product.header-actions.delete.notification.title'))
                        ->body(__('products::filament/resources/product/pages/edit-product.header-actions.delete.notification.body')),
                ),
        ];
    }

    protected function afterSave(): void
    {
        $this->getRecord()->variants->each(function ($variant) {
            $variant->update([
                'is_storable' => $this->getRecord()->is_storable,
            ]);
        });
    }
}
