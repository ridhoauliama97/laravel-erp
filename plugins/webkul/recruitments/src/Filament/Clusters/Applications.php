<?php

namespace Webkul\Recruitment\Filament\Clusters;

use Filament\Clusters\Cluster;

class Applications extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function getSlug(): string
    {
        return 'recruitments/applications';
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/applications.navigation.group');
    }
}
