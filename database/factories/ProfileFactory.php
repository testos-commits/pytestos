<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'description' => $this->faker->paragraph,
            'country_id' => 1,
            'date_of_birth' => $this->faker->date(),
            'city' => $this->faker->city,
            'street' => $this->faker->streetName,
            'building' => $this->faker->buildingNumber,
            'phone' => $this->faker->phoneNumber,
            'gender' => 'male',
            'major' => $this->faker->jobTitle,
            'university' => $this->faker->company,
            'occupation' => $this->faker->jobTitle,
            'height' => $this->faker->randomDigit,
            'weight' => $this->faker->randomDigit,
            'shoes' => $this->faker->randomDigit,
            'pants' => $this->faker->randomDigit,
            'shirt' => $this->faker->randomDigit,
            'video' => $this->faker->url,
            'is_default' => false,
            'meta' => []
        ];
    }
}
