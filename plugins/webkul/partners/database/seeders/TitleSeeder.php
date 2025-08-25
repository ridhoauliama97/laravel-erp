<?php

namespace Webkul\Partner\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class TitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('partners_titles')->delete();

        $user = User::first();

        DB::table('partners_titles')->insert([
            [
                'name'       => 'Doctor',
                'short_name' => 'Dr.',
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Madam',
                'short_name' => 'Mrs',
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Miss',
                'short_name' => 'Miss',
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Mister',
                'short_name' => 'Mr.',
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Professor',
                'short_name' => 'Prof.',
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
