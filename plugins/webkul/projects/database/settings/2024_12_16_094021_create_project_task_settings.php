<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('task.enable_recurring_tasks', false);
        $this->migrator->add('task.enable_task_dependencies', false);
        $this->migrator->add('task.enable_project_stages', false);
        $this->migrator->add('task.enable_milestones', true);
    }

    public function down(): void
    {
        $this->migrator->delete('task.enable_recurring_tasks');
        $this->migrator->delete('task.enable_task_dependencies');
        $this->migrator->delete('task.enable_project_stages');
        $this->migrator->delete('task.enable_milestones');
    }
};
