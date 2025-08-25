<?php

namespace Webkul\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Models\Packaging;
use Webkul\Security\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Webkul\Product\Models\Packaging>
 */
class PackagingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Packaging::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'full_name'  => fake()->name(),
            'creator_id' => User::factory(),
        ];
    }
}
