<?php

namespace Webkul\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class EmployeeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees_categories')->delete();

        $user = User::first();

        $employeesCategories = [
            ['creator_id' => $user?->id, 'name' => 'Sales', 'color' => fake()->hexColor(), 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'name' => 'Trainer', 'color' => fake()->hexColor(), 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'name' => 'Employee', 'color' => fake()->hexColor(), 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'name' => 'Consultant', 'color' => fake()->hexColor(), 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('employees_categories')->insert($employeesCategories);
    }
}
