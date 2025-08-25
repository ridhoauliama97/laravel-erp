<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\DepartureReason;

class DepartureReasonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DepartureReason::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sequence'    => $this->faker->randomNumber(),
            'reason_code' => $this->faker->word,
            'name'        => $this->faker->word,
        ];
    }
}
