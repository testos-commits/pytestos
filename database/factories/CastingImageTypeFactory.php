<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CastingImageTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'name_ar' => $this->faker->name,
            'description' => $this->faker->text,
            'description_ar' => $this->faker->text,
            'image' => $this->faker->imageUrl(),
        ];
    }
}
