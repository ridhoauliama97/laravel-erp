<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Tag;
use Webkul\Security\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Webkul\Inventory\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'color'      => fake()->hexColor(),
            'creator_id' => User::factory(),
        ];
    }
}
