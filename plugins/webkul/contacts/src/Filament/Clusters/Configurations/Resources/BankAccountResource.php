<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources;

use Webkul\Contact\Filament\Clusters\Configurations;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource\Pages;
use Webkul\Partner\Filament\Resources\BankAccountResource as BaseBankAccountResource;

class BankAccountResource extends BaseBankAccountResource
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('contacts::filament/clusters/configurations/resources/bank-account.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('contacts::filament/clusters/configurations/resources/bank-account.navigation.title');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBankAccounts::route('/'),
        ];
    }
}
