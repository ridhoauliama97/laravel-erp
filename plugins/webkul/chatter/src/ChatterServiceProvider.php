<?php

namespace Webkul\Chatter;

use Livewire\Livewire;
use Webkul\Chatter\Livewire\ChatterPanel;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class ChatterServiceProvider extends PackageServiceProvider
{
    public static string $name = 'chatter';

    public static string $viewNamespace = 'chatter';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2024_12_11_101222_create_chatter_followers_table',
                '2024_12_23_062355_create_chatter_messages_table',
                '2024_12_23_080148_create_chatter_attachments_table',
                '2025_03_12_072356_add_column_is_read_to_chatter_messages_table',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Livewire::component('chatter-panel', ChatterPanel::class);
    }
}
