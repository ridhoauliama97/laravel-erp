<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\State;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'                  => $this->faker->company(),
            'company_id'            => $this->faker->uuid(),
            'tax_id'                => $this->faker->bothify('??-########'),
            'registration_number'   => $this->faker->randomNumber(8, true),
            'email'                 => $this->faker->unique()->companyEmail(),
            'phone'                 => $this->faker->phoneNumber(),
            'mobile'                => $this->faker->e164PhoneNumber(),
            'logo'                  => $this->faker->imageUrl(200, 200, 'business', true, 'company logo'),
            'color'                 => $this->faker->hexColor(),
            'is_active'             => $this->faker->boolean(),
            'founded_date'          => $this->faker->date('Y-m-d', '-10 years'),
            'user_id'               => 1,
            'currency_id'           => $this->faker->randomElement([1, 2, 3]),
        ];
    }
}
