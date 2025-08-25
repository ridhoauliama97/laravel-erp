<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('inventories_warehouse.enable_locations', false);
        $this->migrator->add('inventories_warehouse.enable_multi_steps_routes', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('inventories_warehouse.enable_locations');
        $this->migrator->deleteIfExists('inventories_warehouse.enable_multi_steps_routes');
    }
};
